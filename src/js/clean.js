function(expr, typeIfNoOps) {
    typeIfNoOps = typeIfNoOps !== undefined ? typeIfNoOps : false;
    var hasOps = false;
    var isOp = false;

    for (var key in expr) {
        isOp = "$" === key.charAt(0);
        hasOps |= isOp;

        // Clean expressions within logical operators
        if ("$and" === key || "$nor" === key || "$or" === key) {
            expr[key] = expr[key].map(function(expr){
                return clean(expr);
            });
        }

        // Clean within objects
        else if (expr[key] === Object(expr[key])) {
            expr[key] = clean(expr[key], true);
        }

        // Convert other values to a string describing their type
        else {
            expr[key] = toString.call(expr[key]);
        }
    }

    return typeIfNoOps && !hasOps ? toString.call(expr) : expr;
};
