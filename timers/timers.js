function rowaction(value, row, index) {
  var output = '<div class="form-inline" style="white-space: nowrap !important;" role="form">';
    if(row.ENABLED == 1) {
      output = output + '<button class="btn btn-warning" data-toggle="modal" href="d.php?a=disable&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Disable"><i class="fa fa-toggle-on"></i></button>&nbsp;';
    } else {
      output = output + '<button class="btn btn-success" data-toggle="modal" href="d.php?a=enable&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Enable"><i class="fa fa-toggle-off"></i></button>&nbsp;';
    }
    output = output + '<button class="btn btn-default" data-toggle="modal" href="d.php?a=edit&i=' + row.ID +'" data-target="#modal"><i class="fa fa-edit"></i> Edit</button>&nbsp;';
    output = output + '<button class="btn btn-danger" data-toggle="modal" href="d.php?a=del&i=' + row.ID +'" data-target="#modal"><i class="fa fa-trash"></i></button>';
    output = output + '</div>';
  return output;
}

function rowmode(value, row, index) {
  if(row.MODE == 'PING') {
    return 'Automatic';
  } else {
    return 'Chat Activity';
  }
}

function rowStyle(row, index) {
    if (row.ENABLED == 0) {
      return {
        classes: 'warning'
      };
    }
  return {};
}