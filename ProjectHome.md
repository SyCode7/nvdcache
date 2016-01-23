What is nvdCache?

This is a REST style cache for data found in the National Vulnerability Database (NVD).  It uses MySQL as the DB, but this could be any DB provided the common DB functions are adjusted. It receives requests via post variables in the calling URL and returns XML describing the item. This was created to provide an XML feed for single NVD items rather than the current NVD "feed" method (bulk).  It has a secondary goal to be separate source of the NVD data if NVD goes down (the cache part of the name).

If you have any questions please email hurskgroup@hursk.com