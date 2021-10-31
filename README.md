# MediaInfo Parser
Parse mediainfo text into PHP Array - I made a couple of small changes to fit my needs.

[Original repo](https://github.com/bhutanio/mediainfo-parser) - Thanks to @abixalmon



# Usage

```
<?php

$parser = new MediaInfo();

$media_info = '
General
Complete name               : Video.mp4
Format                      : MPEG-4
Format profile              : Base Media
Codec ID                    : isom (isom/iso2/avc1/mp41)
File size                   : 322 MiB
Duration                    : 17 min 27 s
Overall bit rate            : 2 577 kb/s
Writing application         : Lavf57.82.100

Video
ID                          : 1
Format                      : AVC
Format/Info                 : Advanced Video Codec
Format profile              : High@L5
Format settings, CABAC      : Yes
Format settings, ReFrames   : 5 frames
Codec ID                    : avc1
Codec ID/Info               : Advanced Video Coding
Duration                    : 17 min 27 s
Bit rate                    : 2 440 kb/s
Width                       : 1 920 pixels
Height                      : 1 080 pixels
Display aspect ratio        : 16:9
Frame rate mode             : Constant
Frame rate                  : 30.000 FPS
Color space                 : YUV
Chroma subsampling          : 4:2:0
Bit depth                   : 8 bits
Scan type                   : Progressive
Bits/(Pixel*Frame)          : 0.039
Stream size                 : 305 MiB (95%)
Writing library             : x264 core 148
Encoding settings           : cabac=1 / ref=5 / deblock=1:0:0 / analyse=0x3:0x113 / me=hex / subme=8 / psy=1 / psy_rd=1.00:0.00 / mixed_ref=1 / me_range=16 / chroma_me=1 / trellis=2 / 8x8dct=1 / cqm=0 / deadzone=21,11 / fast_pskip=1 / chroma_qp_offset=-2 / threads=34 / lookahead_threads=5 / sliced_threads=0 / nr=0 / decimate=1 / interlaced=0 / bluray_compat=0 / constrained_intra=0 / bframes=3 / b_pyramid=2 / b_adapt=1 / b_bias=0 / direct=3 / weightb=1 / open_gop=0 / weightp=2 / keyint=90 / keyint_min=46 / scenecut=0 / intra_refresh=0 / rc_lookahead=50 / rc=crf / mbtree=1 / crf=22.0 / qcomp=0.60 / qpmin=0 / qpmax=69 / qpstep=4 / vbv_maxrate=4000 / vbv_bufsize=133 / crf_max=25.0 / nal_hrd=none / filler=0 / ip_ratio=1.40 / aq=1:1.00

Audio
ID                          : 2
Format                      : AAC
Format/Info                 : Advanced Audio Codec
Format profile              : LC
Codec ID                    : 40
Duration                    : 17 min 27 s
Duration_LastFrame          : -11 ms
Bit rate mode               : Constant
Bit rate                    : 128 kb/s
Channel(s)                  : 2 channels
Channel positions           : Front: L R
Sampling rate               : 44.1 kHz
Frame rate                  : 43.066 FPS (1024 spf)
Compression mode            : Lossy
Stream size                 : 16.0 MiB (5%)
Default                     : Yes
Alternate group             : 1
';

$data = $parser->parse($text);

print_r($data);
/**
Array
(
    [general] => Array
        (
            [file_name] => video.mp4
            [format] => mpeg-4
            [file_size] => 337641472
            [duration] => 17 min 27 s
            [bit_rate] => 2577kb/s
        )

    [video] => Array
        (
            [0] => Array
                (
                    [format] => avc
                    [codec] => avc1
                    [bit_rate] => 2440kb/s
                    [width] => 1920
                    [anamorphic_width] =>
                    [height] => 1080
                    [anamorphic_height] =>
                    [aspect_ratio] => 16:9
                    [framerate_mode] => constant
                    [frame_rate] => 30.000 fps
                    [bit_depth] => 8 bits
                    [bit_pixel_frame] => 0.039
                    [stream_size] => 319815680
                    [writing_library] => x264 core 148
                    [encoding_settings] => cabac=1 / ref=5 / deblock=1:0:0 / analyse=0x3:0x113 / me=hex / subme=8 / psy=1 / psy_rd=1.00:0.00 / mixed_ref=1 / me_range=16 / chroma_me=1 / trellis=2 / 8x8dct=1 / cqm=0 / deadzone=21,11 / fast_pskip=1 / chroma_qp_offset=-2 / threads=34 / lookahead_threads=5 / sliced_threads=0 / nr=0 / decimate=1 / interlaced=0 / bluray_compat=0 / constrained_intra=0 / bframes=3 / b_pyramid=2 / b_adapt=1 / b_bias=0 / direct=3 / weightb=1 / open_gop=0 / weightp=2 / keyint=90 / keyint_min=46 / scenecut=0 / intra_refresh=0 / rc_lookahead=50 / rc=crf / mbtree=1 / crf=22.0 / qcomp=0.60 / qpmin=0 / qpmax=69 / qpstep=4 / vbv_maxrate=4000 / vbv_bufsize=133 / crf_max=25.0 / nal_hrd=none / filler=0 / ip_ratio=1.40 / aq=1:1.00
                )

        )

    [audio] => Array
        (
            [0] => Array
                (
                    [format] => aac
                    [format_profile] => lc
                    [codec] => 40
                    [bit_rate] => 128kb/s
                    [channels] => 2.0ch
                    [stream_size] => 16777216
                )

        )

    [text] =>
)
**/
