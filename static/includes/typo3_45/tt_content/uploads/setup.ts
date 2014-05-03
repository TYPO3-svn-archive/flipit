includeLibs {
  tx_flipit_cssstyledcontent = EXT:flipit/lib/cssstyledcontent/class.tx_flipit_cssstyledcontent.php
}


plugin.tx_flipit {
  tt_content {
    uploads {
      20 = USER
      20 {
        userFunc = tx_flipit_cssstyledcontent->render_uploads
        field = media
        filePath.field = select_key
        # Rendering for each file (e.g. rows of the table) as a cObject
        itemRendering = COA
        itemRendering {
          wrap = <tr class="tr-odd tr-first">|</tr> |*| <tr class="tr-even">|</tr> || <tr class="tr-odd">|</tr> |*|

          10 = TEXT
          10 {
            data = register:linkedIcon
            wrap = <td class="csc-uploads-icon">|</td>
            if.isPositive.field = layout
          }

          20 = COA
          20 {
            wrap = <td class="csc-uploads-fileName">|</td>
            1 = TEXT
            1 {
              data = register:linkedLabel
              wrap = <p>|</p>
            }
            2 = TEXT
            2 {
              data = register:description
              wrap = <p class="csc-uploads-description">|</p>
              required = 1
              htmlSpecialChars = 1
            }
          }

          30 = TEXT
          30 {
            if.isTrue.field = filelink_size
            data = register:fileSize
            wrap = <td class="csc-uploads-fileSize">|</td>
            bytes = 1
            bytes.labels = {$styles.content.uploads.filesizeBytesLabels}
          }
        }
        useSpacesInLinkText = 0
        stripFileExtensionFromLinkText = 0
        color {
          default =
          1 = #EDEBF1
          2 = #F5FFAA
        }
        tableParams_0 {
          border =
          cellpadding =
          cellspacing =
        }
        tableParams_1 {
          border =
          cellpadding =
          cellspacing =
        }
        tableParams_2 {
          border =
          cellpadding =
          cellspacing =
        }
        tableParams_3 {
          border =
          cellpadding =
          cellspacing =
        }
        linkProc {
          target = _blank
          jumpurl = {$styles.content.uploads.jumpurl}
          jumpurl.secure = {$styles.content.uploads.jumpurl_secure}
          jumpurl.secure.mimeTypes = {$styles.content.uploads.jumpurl_secure_mimeTypes}
          removePrependedNumbers = 1

          iconCObject = IMAGE
          iconCObject.file.import.data = register : ICON_REL_PATH
          iconCObject.file.width = 150
        }
        filesize {
          bytes = 1
          bytes.labels = {$styles.content.uploads.filesizeBytesLabels}
        }
        stdWrap {
          editIcons = tt_content: media, layout [table_bgColor|table_border|table_cellspacing|table_cellpadding], filelink_size, imagecaption
          editIcons.iconTitle.data = LLL:EXT:css_styled_content/pi1/locallang.xml:eIcon.filelist

          prefixComment = 2 | File list:
        }
      }
    }
  }
}