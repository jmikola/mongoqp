/**
 * Returns the canonical "skeleton" of a query document.
 *
 * Values containing one or more expressions should be processed recursively.
 * All other values are reduced to a string denoting their type. Keys in the
 * resulting "skeleton" will be ordered lexicographically.
 */
function(expr) {
    var properties = Object.keys(expr).sort();
    var skeleton = {};

    for (var i = 0; i < properties.length; i++) {
        var key = properties[i];
        var value = expr[key];

        // Convert non-object values to a string describing their type
        if (typeof value !== "object" || value === null) {
            skeleton[key] = toString.call(value).slice(8, -1);
            continue;
        }

        // Recurse for operators whose value is an expression
        if (key in { $elemMatch: 1, $not: 1 }) {
            skeleton[key] = this.apply(this, [value]);
            continue;
        }

        // Recurse for operators whose value is an array of expressions
        if (key in { $and: 1, $nor: 1, $or: 1 }) {
            skeleton[key] = value.map(function(expr){
                return this.apply(this, [expr]);
            }, this);
            continue;
        }

        // Recurse for object values that include an operator
        if (Object.keys(value).some(function(key) { return "$" === key.charAt(0); })) {
            skeleton[key] = this.apply(this, [value]);
            continue;
        }

        // Convert simple objects to a string describing their type
        skeleton[key] = toString.call(value).slice(8, -1);
    }

    return skeleton;
};
