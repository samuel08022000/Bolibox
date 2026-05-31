from flask import Flask, request, jsonify, render_template
from flask_cors import CORS
from mi_scraper import obtener_producto
from currency import obtener_dolar
from ai import procesar_con_ia 

app = Flask(__name__)
CORS(app) 

# Declaramos el estado del usuario una sola vez
user_state = {
    "step": "inicio",
    "producto": None,
    "total_bs": 0,
    "link": None,
    "precio_usd": 0
}

@app.route("/")
def index():
    return render_template("chat.html")

@app.route("/chat", methods=["POST"])
def chat():
    data_input = request.get_json()
    mensaje = data_input.get("message")

    # --- NUEVO: DETECTAR PRECIO MANUAL ---
    es_numero = mensaje.replace('.', '', 1).isdigit()

    if es_numero:
        precio_usd = float(mensaje)
        tasa = obtener_dolar() 
        comision = precio_usd * 0.10 
        envio_local = 120 
        total_bs = ((precio_usd + comision) * tasa) + envio_local

        user_state["step"] = "cotizado"
        user_state["producto"] = "Producto (Ingreso manual)"
        user_state["total_bs"] = total_bs
        user_state["link"] = "Ingreso Manual"
        user_state["precio_usd"] = precio_usd

        detalle_manual = (
            f"✅ ¡Entendido! Aquí tienes el detalle para tu cotización de ${precio_usd:.2f} USD:\n\n"
            f"💵 **Precio producto:** ${precio_usd:.2f} USD\n"
            f"📈 **Tipo de cambio:** {tasa:.2f} Bs/USD\n"
            f"⚙️ **Comisión BoliBox (10%):** ${(comision):.2f} USD\n"
            f"🚚 **Envío local:** {envio_local} Bs\n\n"
            f"💰 **TOTAL A DEPOSITAR: {total_bs:.2f} Bs**\n\n"
            f"¿Confirmamos el pedido para proceder? ✨"
        )
        return jsonify({"response": detalle_manual})

    respuesta_ia = procesar_con_ia(mensaje, [])

    if respuesta_ia == "SOLICITUD_COTIZACION":
        data = obtener_producto(mensaje)

        # 1. EL ESCUDO: Verificamos si data falló o viene vacío
        if data is None:
            return jsonify({
                "response": "Lo siento, la tienda bloqueó temporalmente la lectura del enlace o hubo un error de conexión. 😥 ¿Podrías darme el precio estimado manualmente en dólares para hacerte la cotización?"
            })

        # --- NUEVO: Identificar la tienda ---
        url_min = mensaje.lower()
        tienda = "la tienda"
        if "ebay" in url_min: tienda = "eBay"
        elif "amazon" in url_min: tienda = "Amazon"
        elif "aliexpress" in url_min: tienda = "AliExpress"

        # --- CORRECCIÓN DEL BUG: Usar "precio" en lugar de "precio_usd" ---
        nombre_extraido = data.get("nombre", "Producto desconocido")
        precio_extraido = data.get("precio", 0) 

        # 2. Si pasamos el escudo y tenemos un precio mayor a 0
        if precio_extraido > 0: 
            tasa = obtener_dolar() 
            comision = precio_extraido * 0.10 
            envio_local = 120 
            total_bs = ((precio_extraido + comision) * tasa) + envio_local 

            user_state["step"] = "cotizado"
            user_state["producto"] = nombre_extraido
            user_state["total_bs"] = total_bs
            user_state["link"] = mensaje
            user_state["precio_usd"] = precio_extraido

            detalle = (
                f"📦 *{nombre_extraido}*\n"
                f"🏪 Tienda: {tienda}\n"
                f"💵 Precio: ${precio_extraido:.2f} USD\n"
                f"📈 Tasa: {tasa:.2f} Bs/USD\n"
                f"⚙️ Comisión (10%): ${(comision):.2f} USD\n"
                f"🚚 Envío: {envio_local} Bs\n"
                f"💰 *Total a pagar: {total_bs:.2f} Bs*\n\n"
                f"¿Confirmamos el pedido?"
            )
            return jsonify({"response": detalle})

        else:
            # 3. EL NUEVO MENSAJE DETALLADO QUE PEDISTE (Si entra a la página pero no halla el precio)
            return jsonify({
                "response": f"✅ Logré entrar a **{tienda}** y encontré tu producto:\n"
                            f"📦 _{nombre_extraido}_\n\n"
                            f"❌ Sin embargo, la tienda tiene el precio en un formato oculto y no pude extraerlo exacto. 😢\n"
                            f"¿Podrías enviarme el precio estimado en USD de forma manual (ej. 15.99) para hacerte la cotización?"
            })

    if respuesta_ia == "CONFIRMAR_PEDIDO" and user_state["step"] == "cotizado":
        user_state["step"] = "esperando_datos" 
        
        return jsonify({
            "response": "¡Excelente! Para procesar tu pedido en BoliBox, por favor envíame los siguientes datos en un solo mensaje:\n\n"
                        "👤 *Nombre Completo*\n"
                        "🆔 *Cédula de Identidad*\n"
                        "📍 *Ciudad de envío*\n"
                        "📱 *Número de contacto*"
        })

    if user_state["step"] == "esperando_datos":
        datos_cliente = mensaje 
        user_state["step"] = "confirmado"
        
        return jsonify({
            "response": f"✅ *¡Pedido registrado con éxito!* \n\n"
                        f"Hemos recibido tus datos. Un asesor humano revisará la cotización de "
                        f"*{user_state['producto']}* por un total de {user_state['total_bs']:.2f} Bs "
                        f"y te contactará de inmediato. ¡Gracias por elegir BoliBox! ✨",
            "status": "success",
            "producto": user_state["producto"],
            "total": user_state["total_bs"],
            "link": user_state["link"],
            "precio_usd": user_state["precio_usd"]
        })

    if respuesta_ia == "CONFIRMAR_PEDIDO":
        return jsonify({
            "response": "Primero necesito el link del producto 📦"
        })

    return jsonify({"response": respuesta_ia})

if __name__ == "__main__":
    # Importante: Correrlo en el puerto 5000
    app.run(port=5000, debug=True)