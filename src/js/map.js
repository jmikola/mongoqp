function() {
    var key = {
        op: this.op,
        ns: this.ns,
        query: {},
        sort: {},
    }

    // Ignore records without a query or command component
    if ( ! ("query" in this || "command" in this)) {
        return;
    }

    if (this.query) {
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

    if (this.op === "command") {
        var command = Object.keys(this.command)[0];

        // Ignore commands targetting system collections
        if (this.command[command].substr(0, 7) === "system.") {
            return;
        }

        // Ignore commands not targetting the filtered collection (if any)
        if (collection !== null && this.command[command] !== collection) {
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

    // Handle old response length
    if (this.reslen) {
        this.responseLength = this.reslen;
    }

    var value = {
        count: 1,
        millis: { min: this.millis, max: this.millis, avg: this.millis },
        nreturned: { min: this.nreturned, max: this.nreturned, avg: this.nreturned },
        nscanned: { min: this.nscanned, max: this.nscanned, avg: this.nscanned },
        responseLength: { min: this.responseLength, max: this.responseLength, avg: this.responseLength },
        ts: { min: this.ts, max: this.ts }
    };

    emit(key, value);
};
