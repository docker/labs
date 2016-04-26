using System;
using System.Text;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Net.Http;
using System.Threading.Tasks;
using System.Net.Http.Headers;
using System.Net.Http.Formatting;
using System.IO;
using System.Diagnostics;
using System.Net;

namespace WebAppForm
{
    public partial class _Default : Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            errorCtl.Text = string.Empty;

            tempStationCtl.Text = Properties.Settings.Default.tempStation;
            windStationCtl.Text = Properties.Settings.Default.windStation;

            UpdateFromService(this).Wait();
        }

        protected void ctrlRefresh_Click(object sender, EventArgs e)
        {
            UpdateFromService(this).Wait();
        }

        static async Task UpdateFromService(_Default pageContext)
        {

            var serviceUrl = Properties.Settings.Default.WeatherServiceUrl;
            try
            {
                using (var client = new HttpClient())
                {
                    client.BaseAddress = new Uri(serviceUrl);
                    client.DefaultRequestHeaders.Accept.Clear();
                    client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));
                    // request
                    {
                        HttpResponseMessage response = client.GetAsync("commuteWeatherTodayTomorrow?tempStation=" + pageContext.tempStationCtl.Text
                                + "&windStation=" + pageContext.windStationCtl.Text
                                + "&toMidpoint=" + pageContext.toMidpointCtl.Text
                                + "&fromMidpoint=" + pageContext.fromMidpointCtl.Text).Result;

                        var weatherDataRoot = await response.Content.ReadAsAsync<WeatherDataRoot>();
                        if ( ! response.IsSuccessStatusCode)
                        {
                            //error check
                            if (weatherDataRoot.weatherData.error != string.Empty)
                            {
                                pageContext.errorCtl.Text = "SERVICE ERR: " + weatherDataRoot.weatherData.error;
                            }
                            else
                            {
                                pageContext.errorCtl.Text = "SERVICE ERR: <unknown>";
                            }

                            return;
                        }

                        //config status
                        pageContext.asOfCtl.Text = weatherDataRoot.weatherData.info.asOf;
                        pageContext.tempStationLocCtl.Text = TruncateLoc(weatherDataRoot.weatherData.info.tempStationLoc);
                        pageContext.windStationLocCtl.Text = TruncateLoc(weatherDataRoot.weatherData.info.windStationLoc);

                        //today: now
                        pageContext.beforeToTempCtl.Text = Math.Round(weatherDataRoot.weatherData.today.to.now.tempF).ToString();
                        pageContext.beforeToWindCtl.Text = GetWindString(weatherDataRoot.weatherData.today.to.now);
                        pageContext.beforeToPrecipRateCtl.Text = Math.Round(weatherDataRoot.weatherData.today.to.now.precipInHr, 1).ToString();
                        pageContext.beforeToTempCtl.Text = Math.Round(weatherDataRoot.weatherData.today.to.now.tempF).ToString();
                        pageContext.beforeToHumCtl.Text = Math.Round(weatherDataRoot.weatherData.today.to.now.humidityPct).ToString();
                        pageContext.beforeToCondCtl.Text = weatherDataRoot.weatherData.today.to.now.conditions;

                        //today: to
                        pageContext.toTempCtl.Text = Math.Round(weatherDataRoot.weatherData.today.to.midpoint.tempF).ToString();
                        pageContext.toWindCtl.Text = GetWindString(weatherDataRoot.weatherData.today.to.midpoint);
                        pageContext.toPrecipRateCtl.Text = Math.Round(weatherDataRoot.weatherData.today.to.midpoint.precipInHr, 1).ToString();
                        pageContext.toTempCtl.Text = Math.Round(weatherDataRoot.weatherData.today.to.midpoint.tempF).ToString();
                        pageContext.toHumCtl.Text = Math.Round(weatherDataRoot.weatherData.today.to.midpoint.humidityPct).ToString();
                        pageContext.toCondCtl.Text = weatherDataRoot.weatherData.today.to.midpoint.conditions;

                        //today: from before
                        pageContext.beforeFromTempCtl.Text = Math.Round(weatherDataRoot.weatherData.today.from.before.tempF).ToString();
                        pageContext.beforeFromWindCtl.Text = GetWindString(weatherDataRoot.weatherData.today.from.before);
                        pageContext.beforeFromPrecipRateCtl.Text = Math.Round(weatherDataRoot.weatherData.today.from.before.precipInHr, 1).ToString();
                        pageContext.beforeFromTempCtl.Text = Math.Round(weatherDataRoot.weatherData.today.from.before.tempF).ToString();
                        pageContext.beforeFromHumCtl.Text = Math.Round(weatherDataRoot.weatherData.today.from.before.humidityPct).ToString();
                        pageContext.beforeFromCondCtl.Text = weatherDataRoot.weatherData.today.from.before.conditions;

                        //today: from
                        pageContext.fromTempCtl.Text = Math.Round(weatherDataRoot.weatherData.today.from.midpoint.tempF).ToString();
                        pageContext.fromWindCtl.Text = GetWindString(weatherDataRoot.weatherData.today.from.midpoint);
                        pageContext.fromPrecipRateCtl.Text = Math.Round(weatherDataRoot.weatherData.today.from.midpoint.precipInHr, 1).ToString();
                        pageContext.fromTempCtl.Text = Math.Round(weatherDataRoot.weatherData.today.from.midpoint.tempF).ToString();
                        pageContext.fromHumCtl.Text = Math.Round(weatherDataRoot.weatherData.today.from.midpoint.humidityPct).ToString();
                        pageContext.fromCondCtl.Text = weatherDataRoot.weatherData.today.from.midpoint.conditions;

                        //tomorrow: before
                        pageContext.beforeToTemp2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.to.before.tempF).ToString();
                        pageContext.beforeToWind2Ctl.Text = GetWindString(weatherDataRoot.weatherData.tomorrow.to.before);
                        pageContext.beforeToPrecipRate2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.to.before.precipInHr, 1).ToString();
                        pageContext.beforeToTemp2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.to.before.tempF).ToString();
                        pageContext.beforeToHum2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.to.before.humidityPct).ToString();
                        pageContext.beforeToCond2Ctl.Text = weatherDataRoot.weatherData.tomorrow.to.before.conditions;

                        //tomorrow: to
                        pageContext.toTemp2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.to.midpoint.tempF).ToString();
                        pageContext.toWind2Ctl.Text = GetWindString(weatherDataRoot.weatherData.tomorrow.to.midpoint);
                        pageContext.toPrecipRate2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.to.midpoint.precipInHr, 1).ToString();
                        pageContext.toTemp2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.to.midpoint.tempF).ToString();
                        pageContext.toHum2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.to.midpoint.humidityPct).ToString();
                        pageContext.toCond2Ctl.Text = weatherDataRoot.weatherData.tomorrow.to.midpoint.conditions;

                        //tomorrow: from before
                        pageContext.beforeFromTemp2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.from.before.tempF).ToString();
                        pageContext.beforeFromWind2Ctl.Text = GetWindString(weatherDataRoot.weatherData.tomorrow.from.before);
                        pageContext.beforeFromPrecipRate2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.from.before.precipInHr, 1).ToString();
                        pageContext.beforeFromTemp2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.from.before.tempF).ToString();
                        pageContext.beforeFromHum2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.from.before.humidityPct).ToString();
                        pageContext.beforeFromCond2Ctl.Text = weatherDataRoot.weatherData.tomorrow.from.before.conditions;

                        //tomorrow: from
                        pageContext.fromTemp2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.from.midpoint.tempF).ToString();
                        pageContext.fromWind2Ctl.Text = GetWindString(weatherDataRoot.weatherData.tomorrow.from.midpoint);
                        pageContext.fromPrecipRate2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.from.midpoint.precipInHr, 1).ToString();
                        pageContext.fromTemp2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.from.midpoint.tempF).ToString();
                        pageContext.fromHum2Ctl.Text = Math.Round(weatherDataRoot.weatherData.tomorrow.from.midpoint.humidityPct).ToString();
                        pageContext.fromCond2Ctl.Text = weatherDataRoot.weatherData.tomorrow.from.midpoint.conditions;
                    }
                }
            }
            catch (Exception ex)
            {
                pageContext.errorCtl.Text = "Error using url '" + serviceUrl + "': " + GetInnermostEx(ex).Message;
            }
        }

        private static Exception GetInnermostEx(Exception ex)
        {
            if (ex.InnerException != null) return GetInnermostEx(ex.InnerException);

            return ex;
        }

        private static string TruncateLoc(string loc)
        {
            const int truncLength = 19;

            return loc.Substring(0, truncLength);
        }

        private static string GetWindString(Data data)
        {
            var windStr = new StringBuilder();

            int windSpeed = Convert.ToInt32(Math.Round(data.windSpeedMph));
            int windGust = Convert.ToInt32(Math.Round(data.windGustMph));
            int windDiff = windGust - windSpeed;

            windStr.Append(windSpeed);
            windStr.Append(" ");
            windStr.Append(data.windDirection);

            if (windDiff > 0)
            {
                windStr.Append(" " + windGust + "G");
            }


            return windStr.ToString();
        }
    }
}