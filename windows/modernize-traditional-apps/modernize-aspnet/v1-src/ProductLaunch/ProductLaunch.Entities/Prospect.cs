namespace ProductLaunch.Entities
{
    public class Prospect
    {
        public int ProspectId { get; set; }
        
        public string FirstName { get; set; }
        
        public string LastName { get; set; }

        public string CompanyName { get; set; }

        public string EmailAddress { get; set; }

        public Role Role { get; set; }

        public Country Country { get; set; }
    }
}
