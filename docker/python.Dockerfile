FROM python:3.10

WORKDIR /app

# Installer les dépendances
COPY /python_service/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copier le script
COPY /python_service .

CMD ["python3", "server.py"]  # Optionnel si le script tourne en serveur
