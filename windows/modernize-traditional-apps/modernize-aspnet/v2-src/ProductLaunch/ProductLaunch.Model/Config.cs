using System;
using System.Collections.Generic;

namespace ProductLaunch.Model
{
    public class Config
    {
        private static Dictionary<string, string> _Values = new Dictionary<string, string>();

        public static string DbConnectionString { get { return Get("DB_CONNECTION_STRING"); } }
        
        private static string Get(string variable)
        {
            if (!_Values.ContainsKey(variable))
            {
                var value = Environment.GetEnvironmentVariable(variable, EnvironmentVariableTarget.Machine);
                if (string.IsNullOrEmpty(value))
                {
                    value = Environment.GetEnvironmentVariable(variable, EnvironmentVariableTarget.Process);
                }
                _Values[variable] = value;
            }
            return _Values[variable];
        }
    }
}