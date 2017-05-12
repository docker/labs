using NATS.Client;
using ProductLaunch.Messaging.Messages;

namespace ProductLaunch.Messaging
{
    public static class MessageQueue
    {

        public static void Publish<TMessage>(TMessage message)
            where TMessage : Message
        {
            using (var connection = CreateConnection())
            {
                var data = MessageHelper.ToData(message);
                connection.Publish(message.Subject, data);
            }
        }

        public static IConnection CreateConnection()
        {
            return new ConnectionFactory().CreateConnection(Config.MessageQueueUrl);
        }
    }
}
