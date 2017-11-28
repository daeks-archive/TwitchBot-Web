function rowaction(value, row, index) {
  var output = '<div class="form-inline" style="white-space: nowrap !important;" role="form">';
    if(row.CONFIG == 1) {
      output = output + '<button class="btn btn-default" data-toggle="modal" href="d.php?a=config&i=' + row.ID +'" data-target="#modal"><i class="fa fa-cog"></i> Config</button>&nbsp;';
    }
    if(row.ENABLED == 1) {
      if(row.CHANNELID == row.CURRENTCHANNELID) {
        output = output + '<button class="btn btn-warning" data-toggle="modal" href="d.php?a=disable&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Disable"><i class="fa fa-toggle-on"></i></button>&nbsp;';
        output = output + '<button class="btn btn-danger" data-toggle="modal" href="d.php?a=del&i=' + row.ID +'" data-target="#modal"><i class="fa fa-trash"></i></button>';
      } else {
        output = output + '<button class="btn btn-warning" data-toggle="modal" href="d.php?a=disable&i=' + row.NAME +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Disable"><i class="fa fa-toggle-on"></i></button>&nbsp;';
        output = output + '<button class="btn btn-danger" disabled data-toggle="modal" href="#" data-target="#modal"><i class="fa fa-trash"></i></button>';
      }
    } else {
      if(row.ID > 0) {
        output = output + '<button class="btn btn-success" data-toggle="modal" href="d.php?a=enable&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Enable"><i class="fa fa-toggle-off"></i></button>&nbsp;';
        if(row.CHANNELID == 0 && row.CHANNELID != row.CURRENTCHANNELID) {
          output = output + '<button class="btn btn-danger" disabled data-toggle="modal" href="#" data-target="#modal"><i class="fa fa-trash"></i></button>';
        } else {
          output = output + '<button class="btn btn-danger" data-toggle="modal" href="d.php?a=del&i=' + row.ID +'" data-target="#modal"><i class="fa fa-trash"></i></button>';
        }
      } else {
        output = output + '<button class="btn btn-success" data-toggle="modal" href="d.php?a=enable&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Enable"><i class="fa fa-toggle-off"></i></button>&nbsp;';
        output = output + '<button class="btn btn-danger" disabled data-toggle="modal" href="#" data-target="#modal"><i class="fa fa-trash"></i></button>';
      }
    }
    output = output + '</div>';
  return output;
}

function rowname(value, row, index) {
  var output = row.DESCRIPTION;
  if(row.ICON != '') {
    output = '<i class="fa fa-' + row.ICON + '"></i> ' + output;
  }
  return output;
}

function rowstate(value, row, index) {
  var output = '<div class="status-icon grad-invalid" data-title="tooltip" data-placement="bottom" title="NO STATE"></div>';
  if(row.ENABLED == 0) {
    if(row.CHANNELID == 0 && row.CHANNELID != row.CURRENTCHANNELID) {
      output = '<div class="status-icon grad-primary" data-title="tooltip" data-placement="bottom" title="INHERITED"></div>';
    } else {
      output = '<div class="status-icon grad-invalid" data-title="tooltip" data-placement="bottom" title="DISABLED"></div>';
    }
  } else {
    if(row.CHANNELID == 0 && row.CHANNELID != row.CURRENTCHANNELID) {
      output = '<div class="status-icon grad-primary" data-title="tooltip" data-placement="bottom" title="INHERITED"></div>';
    } else {
      output = '<div class="status-icon grad-success" data-title="tooltip" data-placement="bottom" title="RUNNING"></div>';
    }
  }
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