<!-- Process Modal -->
<style>
  .picture {
    list-style: none;
    margin: 0;
    max-width: 620px;
    padding: 0;
    cursor: zoom-in;
    width: 100%;
  }
</style>

<link href="https://vjs.zencdn.net/7.8.2/video-js.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.5.0/viewer.css" rel="stylesheet" />

<script src="https://vjs.zencdn.net/7.8.2/video.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.5.0/viewer.js" type="text/javascript"></script>

<div class="modal fade" id="modal-twwvr-process" tabindex="-1" role="dialog" aria-labelledby="modal-twwvr-process-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-twwvr-process-label">Process Visual Record Item</h4>
            </div>
            <div class="modal-body" id="twwvr-process-body">
                <div style="padding: 3px;"><strong>Activity Date:</strong> <span id="activity-date">Activity Date Goes Here</span> <strong>Type:</strong> <span id="record-type">Record Type Goes Here</span> <strong>Job/Ticket:</strong> <span id="operation-num">Operation Number Goes Here</span></div>
                <div style="padding: 3px;"><strong>Employee:</strong> <span id="employee">Employee Goes Here</span> <strong>Description:</strong> <span id="description">Description Goes Here</span></div>
                <br />
                <form id="form-twwvr-process" name="form_twwvr_process">
                    <input type="hidden" name="item_id" id="item-id" value="0">
                    <input type="hidden" name="user_name" id="user-name" value="<?=$data['user']['user_name'];?>">
                </form>

                <div id="twwvr-video-box" style="padding: 5px; float: left; display: none;">
                  <video id='twwvr-video' class='video-js' controls preload='auto' width='640' height='360'
                         poster='/assets/images/main/loading_640x320.png' data-setup='{}'>
                    <p class='vjs-no-js'>
                      To view this video please enable JavaScript, and consider upgrading to a web browser that
                      <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
                    </p>
                  </video>
                </div>
                <div id="twwvr-picture-box" style="padding: 5px; float: left; display: none;">
                  <div id="twwvr-picture">
                    <img class="picture" data-original="/images/loading_640x320.png" src="/assets/images/main/loading_640x320.png" alt="TWW Visual Record">
                  </div>
                </div>
                <div style="float: right">
                  <form id="form-twwvr-tasks" name="form_twwvr_tasks">
                    <div class="form-group" style="width:200px;">
                      <label for="work-percent-done">Work Percentage: </label>
                      <select id="work-percent-done" name="work_percent_done"  class="form-control">
                        <?php for ($i=100; $i>=0; $i--) :?>
                        <option value="<?=$i?>"><?=$i?>%</option>
                        <?php
                          $i -= 9; // by 10's
                        endfor; ?>
                      </select>
                    </div>
                    <div class="form-group" style="width:200px;">
                      <label for="video-comment">Comment:</label>
                      <textarea name="video-comment" id="video-comment" rows="5" cols="26">

                      </textarea>
                    </div>
                  </form>
                  <button id="process-email-link" type="button" class="btn btn-info" style="margin-top: 25px;">Email Link</button>
                  <button id="process-create-task" type="button" class="btn btn-warning" style="margin-top: 25px;">Create Ticket</button>
                </div>
            </div>
            <div style="clear: both;"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
              <button id="process-submit" type="button" style="float: left;" class="btn btn-warning" data-dismiss="modal" onclick="processTWWVRData();">Submit</button>
              <button id="process-delete" type="button" style="float: left; margin-left: 25px;" class="btn btn-danger" data-dismiss="modal" onclick="deleteTWWVRData('process');">Delete</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    videojs('twwvr-video', {
        controls: true,
        autoplay: false,
        preload: 'auto',
        playbackRates: [0.5, 1, 1.5, 2, 3, 4, 5]
    });
</script>
<script>
    $(document).ready(function () {

        $('#modal-twwvr-process').on('hidden.bs.modal', function () {
            stopVideo();
        });

        var picture = document.getElementById('twwvr-picture');
        var viewer = new Viewer(picture, {
            url: 'data-original',
            toolbar: {
                oneToOne: false,
                prev: false,
                play: false,
                next: false,
                download: false
            },
        });
    });
</script>