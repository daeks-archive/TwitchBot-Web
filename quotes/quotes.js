function rowaction(value, row, index) {
  var output = '<div class="form-inline" style="white-space: nowrap !important;" role="form">';
    output = output + '<button class="btn btn-default" data-toggle="modal" href="d.php?a=edit&i=' + row.ID +'" data-target="#modal"><i class="fa fa-edit"></i> Edit</button>&nbsp;';
    output = output + '<button class="btn btn-danger" data-toggle="modal" href="d.php?a=del&i=' + row.ID +'" data-target="#modal"><i class="fa fa-trash"></i></button>';
    output = output + '</div>';
  return output;
}

function rowStyle(row, index) {
    if (row.ENABLED == 0) {
      return {
        classes: 'warning'
      };
    }
  return {};
}