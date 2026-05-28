import re
import os
import json  
import requests
from bs4 import BeautifulSoup
from dotenv import load_dotenv

load_dotenv()
SCRAPER_API_KEY = os.getenv("SCRAPER_API_KEY")

def limpiar_precio(texto):
    if not texto: return 0
    try:
        texto = texto.replace(',', '')
        numeros = re.findall(r'\d+\.\d+|\d+', texto)
        if numeros:
            return float(numeros[0])
        return 0
    except:
        return 0

def obtener_producto(url):
    payload = {
        'api_key': SCRAPER_API_KEY, 
        'url': url,
        'render': 'true' 
    }
    
    try:
        print("BoliBot: Solicitando acceso a la tienda vía ScraperAPI...")
        
        response = requests.get('http://api.scraperapi.com', params=payload)
        
        if response.status_code == 200:
            print("BoliBot: ¡Acceso concedido! HTML limpio recibido.")
            soup = BeautifulSoup(response.text, 'html.parser')
            
            url_min = url.lower()
            nombre = "Producto no encontrado"
            precio = 0
            
            if 'amazon' in url_min:
                nombre_el = soup.find(id='productTitle')
                if nombre_el: nombre = nombre_el.text.strip()
                precio_el = soup.find('span', class_='a-price-whole')
                if precio_el: precio = limpiar_precio(precio_el.text)
                
            elif 'ebay' in url_min:
                nombre_el = soup.find('div', class_='vim x-item-title')
                if nombre_el: nombre = nombre_el.text.strip()
                precio_el = soup.find('div', class_='x-price-primary')
                if precio_el: precio = limpiar_precio(precio_el.text)
                
            elif 'aliexpress' in url_min:
                nombre_el = soup.find('h1', class_='product-title-text')
                if nombre_el: nombre = nombre_el.text.strip()
                precio_el = soup.find('span', class_='product-price-value')
                if precio_el: precio = limpiar_precio(precio_el.text)
                
                # Si la lógica original falla, buscamos en el JSON Oculto (Táctica 2)
                if precio == 0:
                    scripts = soup.find_all('script', type='application/ld+json')
                    for script in scripts:
                        try:
                            data = json.loads(script.string)
                            # Buscamos la oferta ('offers') dentro del JSON
                            if isinstance(data, dict) and 'offers' in data:
                                if 'price' in data['offers']:
                                    precio = float(data['offers']['price'])
                                    break
                            elif isinstance(data, list):
                                for item in data:
                                    if 'offers' in item and 'price' in item['offers']:
                                        precio = float(item['offers']['price'])
                                        break
                        except:
                            continue

            elif 'alibaba' in url_min:
                # 1. Buscar el título
                nombre_el = soup.find('h1')
                if nombre_el: nombre = nombre_el.text.strip()
                
                # 2. Buscar el precio exacto de la captura (span con clase 'price-val')
                precio_el = soup.find('span', class_='price-val')
                
                # Respaldos por si la página de Alibaba tiene otro diseño
                if not precio_el:
                    precio_el = soup.find('span', class_='promotion-price')
                if not precio_el:
                    precio_el = soup.find('span', class_='price')
                    
                # 3. Limpiar y guardar el precio
                if precio_el: 
                    # Sacamos el texto o el atributo 'title' que vimos en tu captura (ej. title="$14.39")
                    texto_precio = precio_el.get('title') if precio_el.get('title') else precio_el.text
                    precio = limpiar_precio(texto_precio)
            else:
                nombre_el = soup.find('h1')
                if nombre_el: nombre = nombre_el.text.strip()
            
            if precio == 0:
                texto_pagina = soup.get_text()
                # Busca cosas como "$15.99" o "US $15.99" en toda la página
                posibles_precios = re.findall(r'(?:US\s*\$|\$)\s*(\d+[.,]?\d*)', texto_pagina)
                if posibles_precios:
                    # Toma el primer precio que encuentre y lo limpia
                    precio = float(posibles_precios[0].replace(',', ''))
                
            return {"precio": precio, "nombre": nombre}
            
        else:
            print(f"🚨 Error de ScraperAPI: Código {response.status_code}")
            return None
            
    except Exception as e:
        print(f"💀 BoliBot detectó un error al hacer scraping: {e}")
        return None