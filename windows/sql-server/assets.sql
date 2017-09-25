
--insert sample assets

INSERT INTO Assets (AssetTypeId, LocationId, PurchaseDate, PurchasePrice, AssetTag, AssetDescription)
VALUES (1, 1, '2016-11-14', '1999.99', 'SC0001', 'New MacBook with Emoji Bar');

INSERT INTO Assets (AssetTypeId, LocationId, PurchaseDate, PurchasePrice, AssetTag, AssetDescription)
VALUES (1, 1, '2016-11-14', '800', 'SC0002', 'Logitech Office Cam');

--select assets

SELECT * FROM Assets;