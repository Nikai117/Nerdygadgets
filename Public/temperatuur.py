import mysql.connector
from sense_hat import SenseHat
from datetime import datetime
import time

db_config = {
    'host': '192.168.1.82',
    'user': 'root',
    'password': '',
    'database': 'NerdyGadgets',
}

sense = SenseHat()

def log_sensor_data():
    temperature = sense.get_temperature()
    ColdRoomTemperatureID = id()
    ColdRoomSensor = 5
    timestamp = datetime.now()

    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        sql = "INSERT INTO sensor_data (timestamp, temperature, humidity, pressure) VALUES (%s, %s, %s, %s, $s, $s)"
        values = (ColdRoomTemperatureID, ColdRoomSensor, timestamp, temperature, timestamp, timestamp)
        cursor.execute(sql, values)

        connection.commit()

        cursor.close()
        connection.close()

        print(f"Data logged successfully at {timestamp}")

    except mysql.connector.Error as err:
        print(f"Error: {err}")

try:
    while True:
        log_sensor_data()
        time.sleep(3)

except KeyboardInterrupt:
    print("Meten gestopt")