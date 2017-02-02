using NATS.Client;
using ProductLaunch.MessageHandlers.IndexProspect.Indexer;
using ProductLaunch.Messaging;
using ProductLaunch.Messaging.Messages.Events;
using System;
using System.Threading;

namespace ProductLaunch.MessageHandlers.IndexProspect
{
    class Program
    {
        private static ManualResetEvent _ResetEvent = new ManualResetEvent(false);

        static void Main(string[] args)
        {
            Console.WriteLine($"Initializing Elasticsearch. url: {Config.ElasticsearchUrl}");
            Index.Setup();

            Console.WriteLine($"Connecting to message queue url: {Messaging.Config.MessageQueueUrl}");
            using (var connection = MessageQueue.CreateConnection())
            {
                var subscription = connection.SubscribeAsync(ProspectSignedUpEvent.MessageSubject);
                subscription.MessageHandler += IndexProspect;
                subscription.Start();
                Console.WriteLine($"Listening on subject: {ProspectSignedUpEvent.MessageSubject}");

                _ResetEvent.WaitOne();
                connection.Close();
            }
        }

        private static void IndexProspect(object sender, MsgHandlerEventArgs e)
        {
            Console.WriteLine($"Received message, subject: {e.Message.Subject}");
            var eventMessage = MessageHelper.FromData<ProspectSignedUpEvent>(e.Message.Data);
            Console.WriteLine($"Indexing prospect, signed up at: {eventMessage.SignedUpAt}; event ID: {eventMessage.CorrelationId}");

            var prospect = new Documents.Prospect
            {
                CompanyName = eventMessage.Prospect.CompanyName,
                CountryName = eventMessage.Prospect.Country.CountryName,
                EmailAddress = eventMessage.Prospect.EmailAddress,
                FullName = $"{eventMessage.Prospect.FirstName} {eventMessage.Prospect.LastName}",
                RoleName = eventMessage.Prospect.Role.RoleName,
                SignUpDate = eventMessage.SignedUpAt
            };
            Index.CreateDocument(prospect);

            Console.WriteLine($"Prospect indexed; event ID: {eventMessage.CorrelationId}");
        }
    }
}
