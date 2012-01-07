if (window.Tg == undefined)
    var Tg = {};

if (Tg.Config == undefined)
    Tg.Config = {};

Tg.Config.FileFactory = {
    urlAddFolder:           "/admin/files/FolderAdd"
    , urlEditFolder:        "/admin/files/FolderEdit"
    , urlDeleteFolder:      "/admin/files/FolderDelete"
    , urlMoveFolder:        "/admin/files/FolderMove"
    , urlFolderList:        '/admin/files/FolderList'
    , urlFolderAddFile:     '/Admin/Files/folderAddFile'
    , urlFolderRemoveFile:  '/Admin/Files/FolderRemoveFile'
    , urlFileImport:        '/admin/Files/FileImport'
    , urlFileUploadValums:  '/Admin/Files/FileUploadValums'
    , urlFolderFileList:    '/Admin/Files/FolderFilelist'
};

Tg.Config.FileBrowser = {
    leftHidden: true // disable folder panel as we haven't implmented adding, deleteing folders yet
}