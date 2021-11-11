
print("Banzaii1");

var cursor = db.requisitions.aggregate([
        {
                "$project": {
                        "_id": 0,
			"id": 1,
                        "provider_id": 1,
                        "status_id": 1,
			"status": 1,
                        "date": 1
                }
        },
	
	{
		"$addFields": {
			"strdate": { "$toDate": {"$multiply": [{"$toLong": "$date"}, 1000]} },

                        "asc" : {
                            "$cond" : {
                                if : { "$in" : ["$status_id", ["01", "02", "03"]] }, then : 1,
                                else  : 2
                            }

                        },

			"order" : {
                            "$cond" : {
				if : { "$in" : ["$status_id", ["01", "02", "03"]] }, then : {"$toLong": "$date"},
                                else  : {"$multiply": [-1, {"$toLong": "$date"}] }
                            }

		        },
		}
	},
	{
		"$sort": {
			"asc": 1,
			"order": 1,
		}
	},

]);

cursor.forEach(printjson);


