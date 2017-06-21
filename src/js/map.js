function() {
    var key = {
        op: this.op,
        ns: this.ns,
        query: {},
        sort: {},
    }

    if (this.op === "insert" || this.op === "getmore") {
        return;
    }

    if (typeof this.query === "object") {
        var collectionName = this.ns.substring(this.ns.indexOf('.') + 1);

        // Handle find command format (3.2 and later)
        if (this.query.find === collectionName && typeof this.query.filter === "object") {
            key.query = this.query.filter;
            key.sort = this.query.sort || {};
        }
        // Handle legacy OP_QUERY format (3.0 and earlier)
        else {
            // Ignore explained queries
            if (this.query.$explain) {
                return;
            }

            /* Older drivers may omit "$" prefix on query/orderby parts.
             * See: https://jira.mongodb.org/browse/DRIVERS-81
             */
            key.query = this.query.$query || this.query.query || this.query;
            key.sort = this.query.$orderby || this.query.orderby || {};
        }
    }

    if (typeof this.command === "object") {
        var command = Object.keys(this.command)[0];

        if (typeof command !== "string") {
            return;
        }

        if (typeof this.command[command] !== "string") {
            return;
        }

        // Ignore commands targetting system collections
        if (this.command[command].substr(0, 7) === "system.") {
            return;
        }

        // Ignore commands not targetting the filtered collection (if any)
        if (typeof collection === "string" && this.command[command] !== collection) {
            return;
        }

        /* Commands are queries on the $cmd collection. Use the command's name
         * and collection as the reported operation and namespace, respectively.
         */
        key.op = command.toLowerCase();
        key.ns = database + "." + this.command[command];

        /* While aggregate, group, mapReduce, and text are also commands that
         * query, we can't easily reduce their arguments. Ignore them for now.
         */
        switch (key.op) {
            case "count":
            case "distinct":
                key.query = this.command.query || {};
                break;

            case "findandmodify":
                key.query = this.command.query || {};
                key.sort = this.command.sort || {};
                break;

            default:
                return;
        }
    }

    if (key.query) {
        key.query = skeleton.apply(skeleton, [key.query]);
    }

    // Handle old responseLength
    if (this.responseLength === undefined && this.reslen !== undefined) {
        this.responseLength = this.reslen;
    }

    // Handle old keysExamined (pre-3.2.0)
    if (this.keysExamined === undefined && this.nscanned !== undefined) {
        this.keysExamined = this.nscanned;
    }

    var value = {
        count: 1,
        millis: { min: this.millis, max: this.millis, avg: this.millis },
        nreturned: { min: this.nreturned, max: this.nreturned, avg: this.nreturned },
        keysExamined: { min: this.keysExamined, max: this.keysExamined, avg: this.keysExamined },
        responseLength: { min: this.responseLength, max: this.responseLength, avg: this.responseLength },
        ts: { min: this.ts, max: this.ts }
    };

    emit(key, value);
};
