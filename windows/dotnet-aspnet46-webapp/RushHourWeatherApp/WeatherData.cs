using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace WebAppForm
{

    public class WeatherDataRoot
    {
        public Weatherdata weatherData { get; set; }
    }


    public class Rootobject
    {
        public Weatherdata weatherData { get; set; }
    }

    public class Weatherdata
    {
        public string error = string.Empty;
        public Info info { get; set; }
        public Today today { get; set; }
        public Tomorrow tomorrow { get; set; }
    }

    public class Info
    {
        public string asOf { get; set; }
        public string tempStationLoc { get; set; }
        public string windStationLoc { get; set; }
    }

    public class Today
    {
        public From from { get; set; }
        public To to { get; set; }
    }

    public class From
    {
        public Data before { get; set; }
        public Data midpoint { get; set; }
    }

    public class Data
    {
        public string conditions { get; set; }
        public float humidityPct { get; set; }
        public float precipInHr { get; set; }
        public float tempF { get; set; }
        public string windDirection { get; set; }
        public float windGustMph { get; set; }
        public float windSpeedMph { get; set; }
    }

    public class To
    {
        public Data midpoint { get; set; }
        public Data now { get; set; }
    }

    public class Tomorrow
    {
        public From1 from { get; set; }
        public To1 to { get; set; }
    }

    public class From1
    {
        public Data before { get; set; }
        public Data midpoint { get; set; }
    }

    public class To1
    {
        public Data before { get; set; }
        public Data midpoint { get; set; }
    }

}