var fs       = require('fs')
var reader   = require('anviz-backup-reader')
var excel    = require('excel-export')

var BASEDATE = new Date(1899,11,30)

module.exports = function(source, output) {
    var conf = {}
    conf.cols = [
        { caption: 'User ID', type: 'number' },
        { caption: 'Timestamp', type: 'date', beforeCellWrite: function (row, cellData, eOpt) { 
            return (cellData-BASEDATE)/(24*60*60*1000) 
        }},
        { caption: 'Type', type: 'string'}
    ]
    conf.rows = reader.readFileSync(source).map(function(item) {
        return [ item.userId, item.time, item.code ]
    })
    fs.writeFileSync(output, excel.execute(conf), 'binary')
}
