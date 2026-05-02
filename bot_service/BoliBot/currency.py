import requests

def obtener_dolar():
    try:
        url = "https://api.exchangerate-api.com/v4/latest/USD"
        response = requests.get(url)
        data = response.json()

        tasa = data["rates"]["BOB"]
        return tasa

    except:
        return 6.96  # fallback Bolivia