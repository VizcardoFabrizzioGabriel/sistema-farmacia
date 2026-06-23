#!/usr/bin/env python3
"""
Heapsort para ordenamiento de inventario EDDUFARMA
Uso: python heapsort_inventario.py [criterio] [json_data]
Criterios: fecha_vencimiento, cantidad_actual, nombre
"""

import sys
import json
import heapq
from datetime import datetime


def heapsort_por_fecha_vencimiento(productos):
    """Ordena productos/lotes por fecha de vencimiento (más próxima primero)"""
    heap = []
    for item in productos:
        fecha = datetime.strptime(item['fecha_vencimiento'], '%Y-%m-%d')
        heapq.heappush(heap, (fecha, item))
    
    resultado = []
    while heap:
        resultado.append(heapq.heappop(heap)[1])
    
    return resultado


def heapsort_por_cantidad(productos):
    """Ordena por cantidad actual (menor primero, para priorizar stock bajo)"""
    heap = []
    for item in productos:
        heapq.heappush(heap, (item['cantidad_actual'], item))
    
    resultado = []
    while heap:
        resultado.append(heapq.heappop(heap)[1])
    
    return resultado


def heapsort_por_nombre(productos):
    """Ordena alfabéticamente por nombre"""
    heap = []
    for item in productos:
        heapq.heappush(heap, (item['nombre'].lower(), item))
    
    resultado = []
    while heap:
        resultado.append(heapq.heappop(heap)[1])
    
    return resultado


def main():
    if len(sys.argv) < 3:
        print(json.dumps({
            'error': 'Uso: python heapsort_inventario.py [criterio] [json_data]',
            'criterios': ['fecha_vencimiento', 'cantidad_actual', 'nombre']
        }))
        sys.exit(1)
    
    criterio = sys.argv[1]
    
    try:
        productos = json.loads(sys.argv[2])
    except json.JSONDecodeError:
        print(json.dumps({'error': 'JSON inválido'}))
        sys.exit(1)
    
    if criterio == 'fecha_vencimiento':
        resultado = heapsort_por_fecha_vencimiento(productos)
    elif criterio == 'cantidad_actual':
        resultado = heapsort_por_cantidad(productos)
    elif criterio == 'nombre':
        resultado = heapsort_por_nombre(productos)
    else:
        print(json.dumps({
            'error': 'Criterio no válido',
            'criterios': ['fecha_vencimiento', 'cantidad_actual', 'nombre']
        }))
        sys.exit(1)
    
    print(json.dumps({
        'success': True,
        'criterio': criterio,
        'total': len(resultado),
        'data': resultado
    }))


if __name__ == '__main__':
    main()