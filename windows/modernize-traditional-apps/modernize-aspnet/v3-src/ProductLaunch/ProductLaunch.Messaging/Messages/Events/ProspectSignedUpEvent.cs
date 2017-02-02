using ProductLaunch.Entities;
using System;

namespace ProductLaunch.Messaging.Messages.Events
{
    public class ProspectSignedUpEvent : Message
    {
        public override string Subject { get { return MessageSubject; } }

        public DateTime SignedUpAt { get; set; }

        public Prospect Prospect { get; set; }

        public static string MessageSubject = "events.prospect.signedup";
    }
}
