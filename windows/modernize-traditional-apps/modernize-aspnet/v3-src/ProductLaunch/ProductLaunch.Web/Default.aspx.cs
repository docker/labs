using System;
using System.IO;
using System.Net;
using System.Web.UI;

namespace ProductLaunch.Web
{
    public partial class _Default : Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            if (!string.IsNullOrEmpty(Config.HomePageUrl))
            {
                Response.Clear();
                var request = HttpWebRequest.Create(Config.HomePageUrl);
                var response = request.GetResponse();
                using (var stream = response.GetResponseStream())
                using (var reader = new StreamReader(stream))
                {
                    var html = reader.ReadToEnd();
                    Response.Write(html);
                }
                Response.End();           
            }
        }        
    }
}