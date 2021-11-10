/*db.requisitions.aggregate([
    { "$project" : {
        "provider_id" : 1,
        "status_id" : 1,
        "order" : {
            "$cond" : {
                if : { "$eq" : ["$status_id", "01"] }, then : 1,
                else  : { "$cond" : {
                    "if" : { "$eq" : ["$status_id", "01"] }, then : 2, 
                    else  : 3
                    }
                }
            }
        }
    } }, 
    {"$sort" : {"order" : 1} },
    { "$project" : { "_id" : 0, "provider_id" : 1, "status_id" : 1 } }
]);
*/

print("Banzaii1");
/*
var cursor = db.requisitions.aggregate([
    { "$project" : {
	"_id": 0,
        "provider_id" : 1,
        "status_id" : 1,
	"date" : 1,
	"status": 1,
        "order" : {
            "$cond" : {
		    if: { "$eq": ["$status_id", "06" ]  },
		    then: 6666, // { "$cond": { if: {"$eq": ["status_id", "03"]}, then: 0, else: -1 } },
		    else: {"$cond": {if: {"$eq": ["$status_id", "03"]}, then: 3333, else: 4444 }}
	    	}
            }
    } },
    //{"$group" : {"_id": ["$provider_id", "$status_id"], "acc": {"$sum" : "$order" } } },
    //{"$match" : {"status_id": {"$eq": "06"} } },
    //{"$unwind": "$all"},
    {"$match": { "status_id": {"$in": ["04", "02"] }}},
    //{"$match" : {  "$or": [ {"status_id": {"$eq": "04"} }, {"status_id": {"eq": "06"} } ]   } }//,
    //{"$group" : {_id: null, all:{$push:"$all"}}}
    {"$sort" : {"status_id" : 1} }//,
    //{ "$project" : { "_id" : 1, "provider_id" : 1, "status_id" : 1, "status": 1, "order": 1 } }
]);

cursor.forEach(printjson);
*/


/*
var cursor = db.requisitions.aggregate([
	{
		"$project": {
			"_id": 1,
			"provider_id": 1,
			"status_id": 1,
			"status": 1,
			"date": 1
		}
	},
	{
		"$match": {
			"status_id": {"$in": ["04", "06"] }
		}
	},
	{
		"$sort": {
			"status_id": -1
		}
	},
	{
		"$match": {
			"provider_id": {"$in": ["00005"] }
		}
	},
	{
		"$sort": {
			"status_id": -1
		}
	}
]);

cursor.forEach(printjson);
*/

var cursor = db.requisitions.aggregate([
        {
                "$project": {
                        "_id": 0,
                        "provider_id": 1,
                        "status_id": 1,
                        "date": 1
                }
        },
	
	{
		"$addFields": {
			//"maxDate": { "$max": "$date" },
			//"strdate": { "$toDate": {"$divide": [{"$toLong": "$date"}, 1]} },
			"strdate": { "$toDate": {"$multiply": [{"$toLong": "$date"}, 1000]} },
			//"strdate": { "$toDate": {"$toLong": "$date"} },

		        "asc" : {
                            "$cond" : {
				    if : { "$in" : ["$status_id", ["01", "02", "03"]] }, then : {"$toInt": "$status_id"},
                                else  : {"$multiply": [-1, {"$toInt": "$status_id"}] }
                            }

		        },
			"order" : {
                            "$cond" : {
				    if : { "$in" : ["$status_id", ["01", "02", "03"]] }, then : {"$toLong": "$date"},
                                else  : {"$multiply": [-1, {"$toLong": "$date"}] }
                            }

		        },

			/*
                        "desc" : {
                            "$cond" : {
                                if : { "$in" : ["$status_id", ["04", "05", "06"] ] }, then : 1,
                                else  : -1
                            }
                        },
			*/
		}
	},
	{
		"$sort": {
			"order": 1,
			"asc": 1
		}
	}
/*	
        {
                "$match": {
                        "desc": {"$in": [1] }
		}
        },
        {
                "$sort": {
                        "status_id": -1
                }
        },
        {
                "$match": {
                        "asc": {"$in": [1] }
                } 
        },
        {
                "$sort": {
                        "status_id": 1
                }
        }
*/

]);

cursor.forEach(printjson);


/**
cur = db.requisitions.find().toArray().sort(function(doc1, doc2) {
	return 1;
  // return doc1.status_id - doc2.status_id
});
*/


// cur.forEach(printjson);
