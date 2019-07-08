import json2csv from 'json2csv'

export default function(data, fields, fieldNames, fileName) {
    try {
        var result = json2csv({ data: data, fields: fields, fieldNames: fieldNames });
        let resarr = result.split(',')

        for (var i in resarr) {
            console.log(resarr[i].substr(resarr[i].length - 3, 2) == '\\' + 't')
            if (resarr[i].substr(resarr[i].length - 3, 2) == '\\' + 't') {
                resarr[i] = resarr[i].substring(0, resarr[i].length - 3) + resarr[i].substring(resarr[i].length - 1, resarr[i].length)
            }
            resarr[i] = resarr[i] + '\t'
        }
        // console.log(resarr)
        result = resarr.join()
        var csvContent = 'data:application/octet-stream;charset=utf-8,\uFEFF' + result;
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `${(fileName || 'file')}.csv`);
        document.body.appendChild(link); // Required for FF
        link.click(); // This will download the data file named "my_data.csv".
        document.body.removeChild(link); // Required for FF

    } catch (err) {
        // Errors are thrown for bad options, or if the data is empty and no fields are provided.
        // Be sure to provide fields if it is possible that your data array will be empty.
        console.error(err);
    }
}
