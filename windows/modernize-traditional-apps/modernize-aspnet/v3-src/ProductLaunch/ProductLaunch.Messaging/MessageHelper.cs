using Newtonsoft.Json;
using ProductLaunch.Messaging.Messages;
using System.Text;

namespace ProductLaunch.Messaging
{
    public class MessageHelper
    {
        public static byte[] ToData<TMessage>(TMessage message)
            where TMessage : Message
        {
            var json = JsonConvert.SerializeObject(message);
            return Encoding.Unicode.GetBytes(json);
        }

        public static TMessage FromData<TMessage>(byte[] data)
            where TMessage : Message
        {
            var json = Encoding.Unicode.GetString(data);
            return (TMessage)JsonConvert.DeserializeObject<TMessage>(json);
        }
    }
}
