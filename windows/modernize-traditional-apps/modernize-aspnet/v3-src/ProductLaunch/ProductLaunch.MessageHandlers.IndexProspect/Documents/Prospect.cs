using System;

namespace ProductLaunch.MessageHandlers.IndexProspect.Documents
{
    public class Prospect
    {
        public string FullName { get; set; }

        public string CompanyName { get; set; }

        public string EmailAddress { get; set; }

        public string RoleName { get; set; }

        public string CountryName { get; set; }

        public DateTime SignUpDate { get; set; }
    }
}
