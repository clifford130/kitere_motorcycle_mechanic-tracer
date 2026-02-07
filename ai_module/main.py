from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import mysql.connector
import math
from typing import List, Optional
import pandas as pd

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Database configuration
db_config = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'kenya_mechanic_db'
}

class DiagnosisRequest(BaseModel):
    symptoms: str
    latitude: float
    longitude: float

class MechanicRecommendation(BaseModel):
    email: str
    full_name: str
    garage_name: str
    distance: float
    rating: float
    experience: int
    services: str
    score: float
    available: bool

# Simple keyword-based symptom classifier
SYMPTOM_MAP = {
    "Oil Change": ["oil", "leak", "dirty", "lube", "filter", "viscosity"],
    "Engine Repair": ["engine", "smoke", "stall", "noise", "knocking", "overheat", "power"],
    "Tire Replacement": ["tire", "flat", "puncture", "grip", "tread", "burst", "wheel"],
    "Brake Adjustment": ["brake", "squeak", "soft", "stopping", "grinding", "pad"],
    "Electrical Diagnostics": ["light", "wiring", "dash", "sensor", "fuse", "short", "horn"],
    "Battery Replacement": ["battery", "start", "dead", "dim", "charging", "voltage"]
}

def diagnose_service(symptoms: str) -> str:
    symptoms = symptoms.lower()
    scores = {service: 0 for service in SYMPTOM_MAP}
    for service, keywords in SYMPTOM_MAP.items():
        for keyword in keywords:
            if keyword in symptoms:
                scores[service] += 1

    # Return the service with the highest score, or "General Repair" if no match
    best_service = max(scores, key=scores.get)
    if scores[best_service] == 0:
        return "General Repair"
    return best_service

def haversine(lat1, lon1, lat2, lon2):
    R = 6371  # Earth radius in kilometers
    dlat = math.radians(lat2 - lat1)
    dlon = math.radians(lon2 - lon1)
    a = math.sin(dlat / 2)**2 + math.cos(math.radians(lat1)) * math.cos(math.radians(lat2)) * math.sin(dlon / 2)**2
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1 - a))
    return R * c

@app.post("/diagnose", response_model=List[MechanicRecommendation])
def diagnose_and_recommend(request: DiagnosisRequest):
    service_needed = diagnose_service(request.symptoms)

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        # Fetch all mechanics and their current bookings for today
        from datetime import date
        today = date.today().isoformat()

        cursor.execute("""
            SELECT m.*,
                   COALESCE(AVG(r.rating), 0) as avg_rating,
                   (SELECT COUNT(*) FROM bookings b WHERE b.mechanic_email = m.email AND b.booking_date = %s AND b.status IN ('confirmed', 'pending')) as active_bookings
            FROM mechanics m
            LEFT JOIN reviews r ON m.email = r.mechanic_email
            GROUP BY m.email
        """, (today,))
        mechanics = cursor.fetchall()

        recommendations = []
        for m in mechanics:
            # Check if mechanic offers the diagnosed service (or if it's general repair)
            offered_services = m['services_offered'].split(', ')
            if service_needed != "General Repair" and service_needed not in offered_services:
                continue

            dist = haversine(request.latitude, request.longitude, float(m['latitude']), float(m['longitude']))

            # Simplified scoring: higher rating, higher experience, lower distance
            # Normalizing distance: let's say max distance considered is 50km
            dist_score = max(0, (50 - dist) / 50)
            rating_score = float(m['avg_rating']) / 5.0
            exp_score = min(1.0, m['experience'] / 20.0) # Assume 20 years is max relevant experience

            final_score = (rating_score * 0.4) + (dist_score * 0.4) + (exp_score * 0.2)

            recommendations.append(MechanicRecommendation(
                email=m['email'],
                full_name=m['full_name'],
                garage_name=m['garage_name'],
                distance=round(dist, 2),
                rating=round(m['avg_rating'], 1),
                experience=m['experience'],
                services=m['services_offered'],
                score=round(final_score * 100, 2),
                available=m['active_bookings'] < 5 # Assume max 5 bookings per day for simplicity
            ))

        # Sort by score descending
        recommendations.sort(key=lambda x: x.score, reverse=True)

        cursor.close()
        conn.close()
        return recommendations

    except mysql.connector.Error as err:
        raise HTTPException(status_code=500, detail=f"Database error: {err}")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
