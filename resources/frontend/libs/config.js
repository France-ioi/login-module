export default function(path) {

    function find(path, obj) {
        var paths = path.split('.'), current = obj, i;
        for(i=0; i<paths.length; ++i) {
            if(current[paths[i]] == undefined) {
                return undefined;
            } else {
                current = current[paths[i]];
            }
        }
        return current;
    }
    return find(path, window.__LOGIN_MODULE_CONFIG);
}