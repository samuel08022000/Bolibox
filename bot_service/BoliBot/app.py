from flask import Flask, request, jsonify, render_template
from flask_cors import CORS
from mi_scraper import obtener_producto
from currency import obtener_dolar
from ai import procesar_con_ia # Nueva integración

app = Flask(__name__)
CORS(app) # <--- ESTO ES VITAL: Permite que PHP se conecte a Python

# Diccionario para manejar estados (En producción se usa base de datos o sesiones)
user_state = {"step": "inicio"}

@app.route("/")
def index():
    return render_template("chat.html")

user_state = {
    "step": "inicio",
    "producto": None,
    "total_bs": 0
}

@app.route("/chat", methods=["POST"])
def chat():
    data_input = request.get_json()
    mensaje = data_input.get("message")

    # --- NUEVO: DETECTAR PRECIO MANUAL ---
    es_numero = mensaje.replace('.', '', 1).isdigit()

    if es_numero:
        precio_usd = float(mensaje)
        tasa = obtener_dolar() # 
        comision = precio_usd * 0.10 # 
        envio_local = 120 # 
        total_bs = ((precio_usd + comision) * tasa) + envio_local

        # Actualizamos el estado para que el bot sepa qué se está cotizando
        user_state["step"] = "cotizado"
        user_state["producto"] = "Producto (Ingreso manual)"
        user_state["total_bs"] = total_bs

        # Construimos el detalle que el cliente verá
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

    # 🔥 CASO 1: EL USUARIO MANDA LINK
    if respuesta_ia == "SOLICITUD_COTIZACION":
        data = obtener_producto(mensaje)

        if data["precio_usd"] > 0:
            tasa = obtener_dolar() # [cite: 2]
            comision = data["precio_usd"] * 0.10 # 
            envio_local = 120 # 
            total_bs = ((data["precio_usd"] + comision) * tasa) + envio_local # [cite: 2]

            # GUARDAMOS ESTADO [cite: 3]
            user_state["step"] = "cotizado"
            user_state["producto"] = data["nombre"]
            user_state["total_bs"] = total_bs

            # Respuesta detallada
            detalle = (
                f"📦 *{data['nombre']}*\n"
                f"💵 Precio: ${data['precio_usd']:.2f} USD\n"
                f"📈 Tasa: {tasa:.2f} Bs/USD\n"
                f"⚙️ Comisión (10%): ${(comision):.2f} USD\n"
                f"🚚 Envío: {envio_local} Bs\n"
                f"💰 *Total a pagar: {total_bs:.2f} Bs*\n\n"
                f"¿Confirmamos el pedido?"
            )
            return jsonify({"response": detalle})

        else:
            return jsonify({"response": "No encontré el precio 😢 ¿me lo pasas en USD?"})

    # 🔥 CASO 2: EL CLIENTE ACEPTA LA COTIZACIÓN
    if respuesta_ia == "CONFIRMAR_PEDIDO" and user_state["step"] == "cotizado":
        user_state["step"] = "esperando_datos" # Cambiamos el estado para bloquear el flujo hasta tener los datos
        
        return jsonify({
            "response": "¡Excelente! Para procesar tu pedido en BoliBox, por favor envíame los siguientes datos en un solo mensaje:\n\n"
                        "👤 *Nombre Completo*\n"
                        "🆔 *Cédula de Identidad*\n"
                        "📍 *Ciudad de envío*\n"
                        "📱 *Número de contacto*"
        })

    # 🔥 CASO 3: REGISTRO DE DATOS Y FINALIZACIÓN
    if user_state["step"] == "esperando_datos":
        # Aquí capturamos el mensaje que contiene sus datos
        datos_cliente = mensaje 
        user_state["step"] = "confirmado"
        
        # En una fase futura, aquí guardarías 'datos_cliente' en tu base de datos [cite: 1]
        
        return jsonify({
            "response": f"✅ *¡Pedido registrado con éxito!* \n\n"
                        f"Hemos recibido tus datos. Un asesor humano revisará la cotización de "
                        f"*{user_state['producto']}* por un total de {user_state['total_bs']:.2f} Bs "
                        f"y te contactará de inmediato. ¡Gracias por elegir BoliBox! ✨"
        })

    # 🔥 CASO 4: SI DICE "SI" PERO NO HAY COTIZACIÓN
    if respuesta_ia == "CONFIRMAR_PEDIDO":
        return jsonify({
            "response": "Primero necesito el link del producto 📦"
        })

    # 🔥 CASO NORMAL
    return jsonify({"response": respuesta_ia})

if __name__ == "__main__":
    # Importante: Correrlo en el puerto 5000
    app.run(port=5000, debug=True)