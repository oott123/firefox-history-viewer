<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8"/>
            <title>Firefox history viewer</title>
            <link rel="stylesheet" href="<?=$rooturl;?>/static/common.css" type="text/css" />
            <base target="_blank"></base>
        </head>
        <body>
            <header>
                Firefox history viewer
            </header>
        <?php if(!$dbcount):?>
            <h1>Welcome to FHV!</h1>
            <p>
                FHV (Firefox history viewer) is a simple php script to view your firefox "places.sqlite" directly.
            </p>
            <p>
                To get started, please place your "places.sqlite" under the "data" floder under the FHV floder.
            </p>
        <?php else:?>
            <section>
                <p>
                    <form method="post" action="<?=$rooturl;?>/search" target="_self">
                        <input type="text" name="keyword" id="keyword" class="full-width large keyword"<?=isset($data)?' value="'.$data['keyword'].'"':''; ?>></input>
                    </form>
                </p>
                <p>
                    You have <strong><?=$dbcount;?></strong> databases now.
                    <span id="stat"<?=isset($data)?' style="display:inline;"':''; ?>>Get <stong id="resultnum"><?=isset($data)?$data['count']:'?'; ?></srtong> results in <stong id="pagenum"><?=isset($data)?$data['pages']:'?'; ?></srtong> pages.</span>
                </p>
                <list class="history-list">
            <?php if(!isset($data)):?>
                    <item>
                        <div class="title"><a href="#">Just type above ...</a></div>
                        <div class="url"><a href="#">and you will get all you want.</a></div>
                    </item>
            <?php else:?>
                <?php foreach ($data['result'] as $result):?>
                    <item>
                        <div class="title"><a href="<?=$result['url'];?>"><?=$result['title'];?></a></div>
                        <div class="url"><a href="<?=$result['url'];?>"><?=$result['url'];?></a></div>
                    </item>
                <?php endforeach;?>
            <?php endif;?>
                </list>
            <?php if(isset($data)):?>
                <pager>
                <form method="post" action="<?=$rooturl;?>/search" target="_self">
                <input type="hidden" name="keyword" id="keyword" class="full-width large keyword" <?=isset($data)?' value="'.$data['keyword'].'"':''; ?>></input>
                <?php for($i=1;$i<=$data['pages'];$i++):?>
                    <?php switch (abs($i-$data['page'])) {
                        case 0:
                            echo '<input name="page" type="button" value="'.$i.'" class="pager current" />';
                            break;
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                            echo '<input name="page" type="submit" value="'.$i.'" class="pager" />';
                            break;
                        case 5:
                            echo '<input name="page" type="button" value="..." class="pager current" />';
                        default:
                            # code...
                            break;
                    }?>
                <?php endfor;?>
                </form>
                </pager>
            <?php endif;?>
            </section>
        <?php endif;?>
        </body>
    </html>
