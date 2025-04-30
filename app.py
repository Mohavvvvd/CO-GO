from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import List, Optional, Union
from datetime import datetime
import heapq, itertools

app = FastAPI()

# ===== MODELS =====

class ReservationIn(BaseModel):
    user_id: str
    resource_id: int
    start: datetime
    end: datetime
    occupied_equipment: List[str]

class RequestData(BaseModel):
    desired_start: datetime
    required_equipment: List[str]
    reservations: List[ReservationIn]

class ReservationResponse(BaseModel):
    user_id: str
    resource_id: int
    start: datetime
    end: datetime
    occupied_equipment: List[str]

class NextAvailabilityItem(BaseModel):
    equipment: str
    next_available_at: Optional[datetime]

class NextAvailabilityResponse(BaseModel):
    next_availability: List[NextAvailabilityItem]

# ===== INTERNAL STRUCTURES & A* =====

class ReservationNode:
    def __init__(self, res: ReservationIn):
        self.res = res
        self.child: List["ReservationNode"] = []

class CoworkingAI:
    def heuristic(self, r: ReservationIn, desired: datetime) -> float:
        return abs((desired - r.end).total_seconds())

    def a_star(self, nodes, desired, required) -> Optional[ReservationIn]:
        counter = itertools.count()
        for start in nodes:
            open_set = [(0, next(counter), start)]
            g_score = {id(start): 0}
            visited = set()

            while open_set:
                _, _, cur = heapq.heappop(open_set)
                r = cur.res
                if r.end <= desired and all(eq in r.occupied_equipment for eq in required):
                    return r
                visited.add(id(cur))
                for nb in cur.child:
                    nid = id(nb)
                    if nid in visited:
                        continue
                    cost = (nb.res.end - r.end).total_seconds()
                    tg = g_score[id(cur)] + cost
                    if nid not in g_score or tg < g_score[nid]:
                        g_score[nid] = tg
                        f = tg + self.heuristic(nb.res, desired)
                        heapq.heappush(open_set, (f, next(counter), nb))
        return None

# ===== ENDPOINT =====

@app.post("/available",response_model=Union[ReservationResponse, NextAvailabilityResponse]
)
def find_available(data: RequestData):
    # Validate
    if not data.required_equipment:
        raise HTTPException(400, "Missing equipment")
    if data.desired_start <= datetime.now():
        raise HTTPException(400, "Desired start must be in the future")

    # Build graph
    nodes = [ReservationNode(r) for r in data.reservations]
    for n in nodes:
        for o in nodes:
            if n is not o and n.res.end <= o.res.start:
                n.child.append(o)

    # 1) Try A* match
    ai = CoworkingAI()
    res = ai.a_star(nodes, data.desired_start, data.required_equipment)
    if res:
        return ReservationResponse(
            user_id=res.user_id,
            resource_id=res.resource_id,
            start=res.start,
            end=res.end,
            occupied_equipment=res.occupied_equipment
        )

    # 2) Compute next_availability for each requested item
    next_list: List[NextAvailabilityItem] = []
    for eq in data.required_equipment:
        ends = [
            r.end
            for r in data.reservations
            if eq in r.occupied_equipment and r.end > data.desired_start
        ]
        earliest = min(ends) if ends else None
        next_list.append(NextAvailabilityItem(
            equipment=eq,
            next_available_at=earliest
        ))

    return NextAvailabilityResponse(next_availability=next_list)