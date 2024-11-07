# YT-DLP-API

phpが動作するサーバーを用意しファイルをアップロードしてください

URLの変数を正しく設定する必要があります。
phpファイルのルートURLを設定する。

YT-DLP:https://github.com/yt-dlp/yt-dlp

## 動かない場合

ディレクトリに書き込み権限があるか確認する

yt-dlpがインストールされpathが通ってるか確認する

ffmpegがインストールされpathが通ってるか確認する



# API 説明: yt-dlp を使用した YouTube ダウンロード API

この API スクリプトは、YouTube のリンクを処理して、クエリパラメータ `setting` に基づいて、ビデオまたはオーディオを様々なフォーマットでダウンロードします。動画や音楽の異なるフォーマットや品質に対応しています。

## 対応する URL フォーマット
この API は、以下の形式の YouTube URL を受け入れます:
- **YouTube 標準動画 URL**: `https://www.youtube.com/watch?v=VIDEO_ID`
- **YouTube 短縮 URL**: `https://youtu.be/VIDEO_ID`
- **YouTube Shorts URL**: `https://www.youtube.com/shorts/VIDEO_ID`
- **検索クエリ**: 検索クエリを指定して動画を直接ダウンロードすることもできます。

## 設定パラメータ
`setting` パラメータは、ダウンロードの種類と品質を制御します:
- **video**: MP4 形式で動画をダウンロードします。
- **video-high**: 最高のビデオとオーディオ品質で動画をダウンロードします。
- **music**: MP3 形式でオーディオを抽出してダウンロードします。
- **music-worst**: 最低品質のオーディオ (MP3 または M4A) をダウンロードします。

## リクエストの例
```php
$url = $_SERVER['REQUEST_URI'];
$setting = $_GET['setting'];
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($searchQuery)) {
    $vid = shell_exec('yt-dlp --get-id "ytsearch:' . $searchQuery . '"');
    $vid = preg_replace('/\s+/', '', $vid);
    $url = '/apps/YouAPhpITube/download.php?vid=' . $vid . '&setting=' . $setting;
}

```


## 制作
**sskrc**

---

今後の改良に関する提案やバグ報告は、お気軽にIssueを通してご連絡ください。
