using System;
using System.Collections.Generic;

namespace ProductLaunch.Core
{
    public class Env
    {
        private static Dictionary<string, string> _Values = new Dictionary<string, string>();

        public static string DbConnectionString { get { return Get("DB_CONNECTION_STRING"); } }
        
        private static string Get(string variable)
        {
            if (!_Values.ContainsKey(variable))
            {
                var value = Environment.GetEnvironmentVariable(variable);
                _Values[variable] = value;
            }
            return _Values[variable];
        }
    }
}