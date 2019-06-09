# Hochwasser Ulm
Die Webseite für das TTN Ulm / LoRaWAN Hochwasserprojekt in Ulm.

Sie zeigt den Status der über die Stadt verteilen (geplant sind mehrere, Stand 
Juni 2019 noch nur ein Sensor) Hochwassersensoren über ein Ampelsystem an.

Ziel ist es, den Status der einzelnen Messpunkte (Brücken, Fahrradwege, etc.) schnell 
und einfach zu visualisieren, damit die entsprechende Situation (kein Hochwasser, 
wenig Wasser, viel Wasser) schnell zu erkennen ist. 

## hochwasser.ttnulm.de

-> Direkt zur Webseite: [https://hochwasser.ttnulm.de]()

## Das Projekt

Die TTN Ulm Community Gruppe hat dieses Projekt zusammen mit der Digitalen Agenda 
der Stadt Ulm zusammen entwickelt. Durch das schon bestehende, flächendeckende
LoRaWAN Netz in Ulm ([https://lora.ulm-digital.com](https://lora.ulm-digital.com)) 
und einem geeigneten Ultraschallsensor ([https://www.decentlab.com/products/ultrasonic-distance-/-level-sensor-for-lorawan]())
liefert der erste Sensor seit Anfang Juni 2019 an der Herdbrücke die ersten 
Abstandsdaten. Weitere Sensoren an der Blau sind geplant.

## Daten
Die Daten werden in der community-verwalteten TICK / Grafana Instanz unter [https://ttndata.cortex-media.de]()
gespeichert. Von dort werden die Daten bezogen, verarbeitet und, je nach dann vorliegendem Fall,
die entsprechende Visualisierung generiert.

## Erkennung von Hochwasser
Der Sensor führt alle 10 Minuten eine Messung durch. Jede einzelne Messung besteht
aus 15 Samples, aus denen der Mittelwert gebildet wird. Ungültige Samples werden 
verworfen (bei unserem Setup eher selten).

Die Entscheidung, ob Hochwasser vorliegt oder nicht, läuft wie folgt ab:

* Berechnung des Medians der letzten 2 Stunden, Werte gerundet auf .5.
* Berechnung des Medians der 2 Stunden vor den letzten 2 Stunden (also -4 bis -2 ab jetzt)
* Vergleich der beiden Medianwerte: Wenn die Differenz > 1.0, dann liegt Hochwasser vor.

Der Differenzwerte von 1.0cm ist aktuell noch per Bauchgefühl geschätzt und ließe sich, 
sobald mehr Erfahrungswerte vorliegen, anpassen.

## Technisches

* Einfache PHP Anwendung (kein Framework, wenig Dependencies)
* InfluxDB als Datenbank
* Dependency Management via Composer   

## TODO
* Rohdaten via API
* Graph für Verlauf