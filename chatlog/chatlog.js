function rowlog(value, row, index) {
  var output = row.VALUE;
  if(row.TYPE == 'WRITE') {
    output = row.VALUE.substring(row.VALUE.indexOf(':')+1);
  } else if (row.TYPE == 'READ') {
    output = row.VALUE.substring(row.VALUE.indexOf(':')+1);
    output = output.substring(output.indexOf(':')+1);
  }
  return output;
}

function rowuser(value, row, index) {
  var output = row.INSERTBY;
  if(('#' + row.INSERTBY.toLowerCase()) == row.CHANNEL.toLowerCase()) {
    output = '<b>' + row.INSERTBY + '</b>';
  }
  return output;
}