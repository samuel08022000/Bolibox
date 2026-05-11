import re
import requests
from bs4 import BeautifulSoup

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

def obtener_producto_ebay(url):
    """Scraper de eBay sin usar Selenium (mucho más rápido y estable)"""
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36",
        "Accept-Language": "en-US,en;q=0.9"
    }
    try:
        response = requests.get(url, headers=headers, timeout=10)
        if response.status_code != 200:
            return None
            
        soup = BeautifulSoup(response.content, "html.parser")
        
        # Intentar obtener el título
        title_tag = soup.find("h1", class_="x-item-title__mainTitle")
        nombre = title_tag.get_text(strip=True) if title_tag else "Producto de eBay"
        
        precio_texto = ""l
        price_tag = soup.select_one(".x-price-primary, .v-price-primary, #prcIsum")
        if price_tag:
            precio_texto = price_tag.get_text(strip=True)
        
        if not precio_texto:
            price_tag = soup.select_one(".ux-textspans--BOLD")
            if price_tag and "$" in price_tag.text:
                precio_texto = price_tag.text

        precio_usd = limpiar_precio(precio_texto)
        
        return {
            "nombre": nombre,
            "precio_usd": precio_usd
        }
    except Exception as e:
        print(f"Error Scraper eBay: {e}")
        return None

def obtener_producto_mercadolibre(url):
    """Uso de la API de Mercado Libre (la forma más profesional)"""
    try:
        # Extraemos el ID del producto (MLA123456)
        match = re.search(r'M[A-Z]{2}-?(\d+)', url, re.IGNORECASE)
        if not match: return None
        
        # Formato de ID para la API (ML + Prefijo Pais + Numero)
        item_id_raw = re.search(r'(M[A-Z]{2})(\d+)', url.replace("-", ""), re.IGNORECASE)
        item_id = item_id_raw.group(0)

        api_url = f"https://api.mercadolibre.com/items/{item_id}"
        r = requests.get(api_url).json()
        
        titulo = r.get("title", "Producto Mercado Libre")
        precio = float(r.get("price", 0))
        moneda = r.get("currency_id", "USD")

        return {
            "nombre": titulo,
            "precio_usd": precio
        }
    except:
        return None

def buscar_precio_google(nombre):
    """Búsqueda de emergencia en Google si el link falla"""
    try:
        query = f"{nombre} price usd"
        url = f"https://www.google.com/search?q={query.replace(' ', '+')}"
        headers = {"User-Agent": "Mozilla/5.0"}
        r = requests.get(url, headers=headers, timeout=5)
        soup = BeautifulSoup(r.text, "html.parser")
        
        texto = soup.get_text()
        match = re.search(r'\$\s?(\d+[\.,]\d{2})', texto)
        if match:
            return limpiar_precio(match.group(0))
        return 0
    except:
        return 0

def obtener_producto(url):
    """Función principal que decide qué scraper usar"""
    url_lower = url.lower()
    
    if "ebay" in url_lower:
        print("🔍 Scrapeando eBay (Modo Ligero)...")
        res = obtener_producto_ebay(url)
    elif "mercadolibre" in url_lower:
        print("🔍 Consultando API Mercado Libre...")
        res = obtener_producto_mercadolibre(url)
    else:
        print("⚠️ Tienda no soportada directamente, buscando en Google...")
        res = {"nombre": "Producto", "precio_usd": buscar_precio_google(url)}

    if res and res["precio_usd"] == 0:
        res["precio_usd"] = buscar_precio_google(res["nombre"])

    return res if res else {"nombre": "Producto no identificado", "precio_usd": 0}