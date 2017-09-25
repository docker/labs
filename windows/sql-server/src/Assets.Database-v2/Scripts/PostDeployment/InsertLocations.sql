IF NOT EXISTS (SELECT TOP 1 1 FROM Locations)
BEGIN

    INSERT INTO [dbo].[Locations] (Country, PostalCode, AddressLine1)
    VALUES ('USA', 'DC 20500', '1600 Pennsylvania Ave NW')

    INSERT INTO [dbo].[Locations] (Country, PostalCode, AddressLine1)
    VALUES ('UK', 'SW1A 0AA', 'Houses of Parliament')

END
GO