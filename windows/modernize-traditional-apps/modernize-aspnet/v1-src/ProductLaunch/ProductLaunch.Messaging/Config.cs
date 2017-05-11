using System;
using System.Collections.Generic;

namespace ProductLaunch.Messaging
{
    public class Config
    {
        private static Dictionary<string, string> _Values = new Dictionary<string, string>();

        public static string MessageQueueUrl { get { return Get("MESSAGE_QUEUE_URL"); } }
        
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