#!flask/bin/python

# Bike Commuter Weather app - REST service

import json
import sys
import logging
import requests
import os
import datetime

from flask import Flask, jsonify, request, make_response
from dateutil import parser

httpSuccessCode = 200

wundergroundBaseUrl = "http://api.wunderground.com/api/"
wundergroundConditions = "/conditions/q/"
wundergroundHourly = "/hourly/q/"
wundergroundHistory = "/history/q/"
wundergroundPwsPrefix = "pws:"
wundergroundJsonSuffix = ".json"

app = Flask(__name__)

# stubbed test data - for backward compat. with older UI
amWeatherData = [
    {
        'time': '06:00',
        'temperatureF': 25,
        'windSpeedMph': 15,
        'windDirection' : 'SSW',
        'precipitation': 'Snow'
    },
    {
        'time': '07:00',
        'temperatureF': 27,
        'windSpeedMph': 13,
        'windDirection' : 'SSW',
        'precipitation': 'Snow'
    },
    {
        'time': '08:00',
        'temperatureF': 31,
        'windSpeedMph': 11,
        'windDirection' : 'SSW',
        'precipitation': 'WintryMix'
    }
]

# stubbed test data - for backward compat. with older UI
pmWeatherData = [
    {
        'time': '16:00',
        'temperatureF': 35,
        'windSpeedMph': 7,
        'windDirection' : 'N',
        'precipitation': 'None'
    },
    {
        'time': '17:00',
        'temperatureF': 34,
        'windSpeedMph': 9,
        'windDirection' : 'N',
        'precipitation': 'Rain'
    },
    {
        'time': '18:00',
        'temperatureF': 33,
        'windSpeedMph': 12,
        'windDirection' : 'N',
        'precipitation': 'Sleet'
    }
]

# stubbed test data, also serves as response data structure definition
returnJson = {
    'error' : '',
    'input' : {
      'toMidPoint' : "07:00",
      'fromMidPoint' : "17:30"
    },
    'info' : {
      'asOf' : "Mar 1, 3:01pm",
      'tempStationLoc' : 'Chicago Bronzeville, Chicago, Illinois',
      'windStationLoc' : 'U.S. Cellular Field/Bridgeport, Chicago, Illinois'
    },
    'today' : {
        'to' : {
            'now' : {
                'tempF' : 1,
                'windSpeedMph' : 10,
                'windGustMph' : 12,
                'windDirection' : 'N',
                'precipInHr' : 0.1,
                'humidityPct' : 50,
                'conditions' : 'clear'
            },
            'midpoint' : {
                'tempF' : 2,
                'windSpeedMph' : 11,
                'windGustMph' : 12,
                'windDirection' : 'NNE',
                'precipInHr' : 0,
                'humidityPct' : 50,
                'conditions' : 'cloudy'
            }
        },
        'from' : {
            'before' : {
                'tempF' : 3,
                'windSpeedMph' : 12,
                'windGustMph' : 12,
                'windDirection' : 'NE',
                'precipInHr' : 0.5,
                'humidityPct' : 50,
                'conditions' : 'rain'
            },
            'midpoint' : {
                'tempF' : 4,
                'windSpeedMph' : 13,
                'windGustMph' : 14,
                'windDirection' : 'E',
                'precipInHr' : 0.2,
                'humidityPct' : 50,
                'conditions' : 'snow'
            }
        }
    },
    'tomorrow' : {
        'to' : {
            'before' : {
                'tempF' : 5,
                'windSpeedMph' : 14,
                'windGustMph' : 14,
                'windDirection' : 'ESE',
                'precipInHr' : 0.1,
                'humidityPct' : 50,
                'conditions' : 'hail'
            },
            'midpoint' : {
                'tempF' : 6,
                'windSpeedMph' : 15,
                'windGustMph' : 16,
                'windDirection' : 'SE',
                'precipInHr' : 0.1,
                'humidityPct' : 50,
                'conditions' : 'sleet'
            }
        },
        'from' : {
            'before' : {
                'tempF' : 7,
                'windSpeedMph' : 16,
                'windGustMph' : 18,
                'windDirection' : 'S',
                'precipInHr' : 0.1,
                'humidityPct' : 50,
                'conditions' : 'wintrymix'
            },
            'midpoint' : {
                'tempF' : 8,
                'windSpeedMph' : 17,
                'windGustMph' : 19,
                'windDirection' : 'SW',
                'precipInHr' : 0.1,
                'humidityPct' : 50,
                'conditions' : 'snow'
            }
        }
    }
}

# endpoints

# legacy endpoint for backward-compat. with older UI
@app.route('/today/amrush', methods=['GET'])
def get_amRush():
    return jsonify({'samples': amWeatherData})

# legacy endpoint for backward-compat. with older UI
@app.route('/today/pmrush', methods=['GET'])
def get_pmRush():
    return jsonify({'samples': pmWeatherData})

# get commute am / pm weather for today & tomorrow
@app.route('/commuteWeatherTodayTomorrow', methods=['GET'])
def get_commuteWeatherTodayTomorrow():
    try:
        wundergroundApiKey = os.environ['WUNDERGROUND_API_KEY']
        if (wundergroundApiKey is None or wundergroundApiKey == ''):
            raise Exception('You must specify a wunderground api key via the WUNDERGROUND_API_KEY environment var')

        # get params
        windStation = request.args.get('windStation')
        tempStation = request.args.get('tempStation')
        toMidpoint = request.args.get('toMidpoint')
        fromMidpoint = request.args.get('fromMidpoint')

        # get current conditions
        tempStationConditions = get_weather_api_data(wundergroundBaseUrl + wundergroundApiKey + wundergroundConditions + wundergroundPwsPrefix + tempStation + wundergroundJsonSuffix)
        windStationConditions = get_weather_api_data(wundergroundBaseUrl + wundergroundApiKey + wundergroundConditions + wundergroundPwsPrefix + windStation + wundergroundJsonSuffix)

        # get forecast
        forecast = get_weather_api_data(wundergroundBaseUrl + wundergroundApiKey + wundergroundHourly + wundergroundPwsPrefix + tempStation + wundergroundJsonSuffix)

        # populate return info
        returnJson['input']['toMidPoint'] = toMidpoint
        returnJson['input']['fromMidPoint'] = fromMidpoint
        # use dateutil parser for greater flexibility in time format (ignore date component)
        toMidpointTime = parser.parse(toMidpoint)
        fromMidpointTime = parser.parse(fromMidpoint)

        asOfString = tempStationConditions['current_observation']['observation_time_rfc822']
        asOfDatetime = parser.parse(asOfString)
        returnJson['info']['asOf'] = asOfString
        returnJson['info']['tempStationLoc'] = tempStationConditions['current_observation']['observation_location']['full']
        returnJson['info']['windStationLoc'] = windStationConditions['current_observation']['observation_location']['full']

        # always populate today to now
        set_from_current(tempStationConditions, windStationConditions, returnJson['today']['to']['now'] )

        # populate today to midpoint
        point = returnJson['today']['to']['midpoint']
        set_default_data(point)
        hourlyForecast = get_hourlyForecastIfExists(forecast, asOfDatetime, 0, 0, toMidpointTime)
        if (hourlyForecast is not None):
            set_from_hourly_forecast(hourlyForecast, point)

        # populate today from before
        point = returnJson['today']['from']['before']
        set_default_data(point)
        hourlyForecast = get_hourlyForecastIfExists(forecast, asOfDatetime, 0, -1, fromMidpointTime)
        if (hourlyForecast is not None):
            set_from_hourly_forecast(hourlyForecast, point)

        # populate today from midpoint
        point = returnJson['today']['from']['midpoint']
        set_default_data(point)
        hourlyForecast = get_hourlyForecastIfExists(forecast, asOfDatetime, 0, 0, fromMidpointTime)
        if (hourlyForecast is not None):
            set_from_hourly_forecast(hourlyForecast, point)

        # tomorrow to before
        point = returnJson['tomorrow']['to']['before']
        set_default_data(point)
        hourlyForecast = get_hourlyForecastIfExists(forecast, asOfDatetime, 1, -1, toMidpointTime)
        if (hourlyForecast is not None):
            set_from_hourly_forecast(hourlyForecast, point)

        # tomorrow to midpoint
        point = returnJson['tomorrow']['to']['midpoint']
        set_default_data(point)
        hourlyForecast = get_hourlyForecastIfExists(forecast, asOfDatetime, 1, 0, toMidpointTime)
        if (hourlyForecast is not None):
            set_from_hourly_forecast(hourlyForecast, point)  # tomorrow from before

        # tomorrow from before
        point = returnJson['tomorrow']['from']['before']
        set_default_data(point)
        hourlyForecast = get_hourlyForecastIfExists(forecast, asOfDatetime, 1, -1, fromMidpointTime)
        if (hourlyForecast is not None):
            set_from_hourly_forecast(hourlyForecast, point)

        # tomorrow from midpoint

        point = returnJson['tomorrow']['from']['midpoint']
        set_default_data(point)
        hourlyForecast = get_hourlyForecastIfExists(forecast, asOfDatetime, 1, 0, fromMidpointTime)
        if (hourlyForecast is not None):
            set_from_hourly_forecast(hourlyForecast, point)

        # return
        return jsonify({'weatherData' : returnJson})
    except Exception as e:
        return make_response(jsonify({'weatherData' : { 'error': str(e)}}), 500)

# find an hourly forecast (if available) for the specified current day offset, hr offset
def get_hourlyForecastIfExists(forecast, asOfDatetime, dayOffset, hrOffset, midpointTime):
# TODO: consider a worst-case temp/wind return for the hr after midpoint
# TODO: Consider taking into account minute component of midpointTime
    calced_datetime = (asOfDatetime + datetime.timedelta(days=dayOffset)).replace(hour=midpointTime.hour)
    calced_datetime += datetime.timedelta(hours=hrOffset)

    for h in forecast['hourly_forecast']:
        f_hr = int(h['FCTTIME']['hour'])
        f_day = int(h['FCTTIME']['mday'])
        if (f_hr == calced_datetime.hour and f_day == calced_datetime.day):
            return h

    return None

# set default "no data available" data
def set_default_data(target):
    target['conditions'] = '-'
    target['humidityPct'] = -1
    target['precipInHr'] = -1
    target['tempF'] = -1
    target['windDirection'] = '-'
    target['windGustMph'] = -1
    target['windSpeedMph'] = -1

# set return json structure from current conditions structure
def set_from_current(tempStation, windStation, target):
    target['conditions'] = tempStation['current_observation']['weather']
    target['humidityPct'] = tempStation['current_observation'][
        'relative_humidity'].replace('%', '')
    target['precipInHr'] = tempStation['current_observation']['precip_1hr_in']
    target['tempF'] = tempStation['current_observation']['temp_f']
    target['windDirection'] = windStation['current_observation']['wind_dir']
    target['windGustMph'] = windStation['current_observation']['wind_gust_mph']
    target['windSpeedMph'] = windStation['current_observation']['wind_mph']

# set return json structure from forecast structure
def set_from_hourly_forecast(forecastHour, target):
# TODO: look at 'snow' *or* 'qpf'
    target['conditions'] = forecastHour['condition']
    target['humidityPct'] = forecastHour['humidity']
    target['precipInHr'] = forecastHour['qpf']['english']
    target['tempF'] = forecastHour['temp']['english']
    target['windDirection'] = forecastHour['wdir']['dir']
    target['windGustMph'] = 0
    target['windSpeedMph'] = forecastHour['wspd']['english']


# get weather api data for specified url
def get_weather_api_data(url):
    response = requests.get(url)
    responseJson = response.json()
    if (response.status_code != httpSuccessCode):
        raise Exception('non-success code ' + str(response.status_code) + ' invoking: ' + url)

    if ('error' in responseJson['response']):
        raise Exception('error "' + responseJson['response']['error']['description'] + '" invoking: ' + url)

    return responseJson

if __name__ == '__main__':
    app.run(debug=False, host='0.0.0.0')