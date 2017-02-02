using NATS.Client;
using ProductLaunch.Messaging;
using ProductLaunch.Messaging.Messages.Events;
using ProductLaunch.Model;
using System;
using System.Linq;
using System.Threading;

namespace ProductLaunch.MessageHandlers.SaveProspect
{
    class Program
    {
        private static ManualResetEvent _ResetEvent = new ManualResetEvent(false);

        static void Main(string[] args)
        {
            Console.WriteLine($"Connecting to message queue url: {Messaging.Config.MessageQueueUrl}");
            using (var connection = MessageQueue.CreateConnection())
            {
                var subscription = connection.SubscribeAsync(ProspectSignedUpEvent.MessageSubject);
                subscription.MessageHandler += SaveProspect;
                subscription.Start();
                Console.WriteLine($"Listening on subject: {ProspectSignedUpEvent.MessageSubject}");

                _ResetEvent.WaitOne();
                connection.Close();
            }
        }

        private static void SaveProspect(object sender, MsgHandlerEventArgs e)
        {
            Console.WriteLine($"Received message, subject: {e.Message.Subject}");
            var eventMessage = MessageHelper.FromData<ProspectSignedUpEvent>(e.Message.Data);
            Console.WriteLine($"Saving new prospect, signed up at: {eventMessage.SignedUpAt}; event ID: {eventMessage.CorrelationId}");

            var prospect = eventMessage.Prospect;
            using (var context = new ProductLaunchContext())
            {
                //reload child objects:
                prospect.Country = context.Countries.Single(x => x.CountryCode == prospect.Country.CountryCode);
                prospect.Role = context.Roles.Single(x => x.RoleCode == prospect.Role.RoleCode);

                context.Prospects.Add(prospect);
                context.SaveChanges();
            }

            Console.WriteLine($"Prospect saved. Prospect ID: {eventMessage.Prospect.ProspectId}; event ID: {eventMessage.CorrelationId}");
        }
    }
}