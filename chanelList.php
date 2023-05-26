<?
include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if ($_REQUEST['city']) {
    $ticketType = $_REQUEST['ticketType'];

    if ($ticketType != 4397 && $ticketType != 7106 && $ticketType != 7107) {
        $ticketType = array(15, 16, 4468);
    }

    $arFilter = array('IBLOCK_ID' => 2, 'CHECK_PERMISSIONS' => 'N');

    $arSelect = array('IBLOCK_ID', 'ID', 'NAME');
    $resCity = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

    while ($ob = $resCity->GetNextElement()) {
        $arFields = $ob->GetFields();

        $arChannelType[] = $arFields;
    }

    $arFilter = array('IBLOCK_ID' => 6, 'CHECK_PERMISSIONS' => 'N', 'PROPERTY_CITY' => $_REQUEST['city'], 'PROPERTY_TICKER_TYPES' => $ticketType, 'ACTIVE' => 'Y');

    $arSelect = array('IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_CITY', 'PROPERTY_POCKET_CHANNELS', 'PROPERTY_CHANEL', 'PROPERTY_TICKER_TYPES');
    $resCity = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    $arChannels = array();
    while ($ob = $resCity->GetNextElement()) {
        $arFields = $ob->GetFields();

        if ($arFields['PROPERTY_TICKER_TYPES_VALUE'] == 15) {
            $arStroka[] = $arFields;

        }
        if ($arFields['PROPERTY_TICKER_TYPES_VALUE'] == 16) {
            $arBanner[] = $arFields;
            if ($arFields['PROPERTY_CHANEL_VALUE'] == null) continue;
            $availableChannels[] = $arFields['PROPERTY_CHANEL_VALUE'];
        }
        if ($arFields['PROPERTY_TICKER_TYPES_VALUE'] == 4468) {
            $arRoliki[] = $arFields;
        }
        if ($arFields['PROPERTY_TICKER_TYPES_VALUE'] == 4397) {
            $arRadio[] = $arFields;
        }
        if ($arFields['PROPERTY_TICKER_TYPES_VALUE'] == 7106) {
            $arInternet[] = $arFields;
        }
        if ($arFields['PROPERTY_TICKER_TYPES_VALUE'] == 7107) {
            $arNewsPaper[] = $arFields;
        }

    }

}
global $USER;

$arFilter = array('IBLOCK_ID' => 19, 'CHECK_PERMISSIONS' => 'N', 'PROPERTY_TICKER_OWNER' => $USER->GetID(), 'ACTIVE' => 'Y', 'PROPERTY_CHANEL' => $availableChannels[0] != null ? $availableChannels : 0);

$arSelect = array('IBLOCK_ID', 'ID', 'NAME', 'PROPERTY_CITY', 'PROPERTY_TICKER_TYPE', 'PROPERTY_TICKER_INFO', 'PROPERTY_CHANEL', 'PROPERTY_POCKET_CHANNELS');
$resTicker = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
while ($ob = $resTicker->GetNextElement()) {
    $arShablon[] = $ob->GetFields();
}
?>

<div class="city__tabs" id="tabs-<?= $_REQUEST['city'][0] ?>">
    <ul class="city__top">
        <? if ($arBanner): ?>
            <li class="city__name"><a href="#tabs-1">Баннер</a></li>
        <? endif ?>
        <? if ($arStroka): ?>
            <li class="city__name"><a href="#tabs-2">Бегущая строка</a></li>
        <? endif ?>
        <? if ($arRoliki): ?>
            <li class="city__name"><a href="#tabs-3">Видеоролики</a></li>
        <? endif ?>
        <? if ($arRadio): ?>
            <li class="city__name"><a href="#tabs-4">Радио</a></li>
        <? endif ?>
        <? if ($arInternet): ?>
            <li class="city__name"><a href="#tabs-5">Интернет</a></li>
        <? endif ?>
        <? if ($arNewsPaper): ?>
            <li class="city__name"><a href="#tabs-6">Печатные издания</a></li>
        <? endif ?>
    </ul>
    <? if ($arBanner): ?>
        <div class="city__field" id="tabs-1">
            <? foreach ($arShablon as $shablon) {
                if ($shablon['PROPERTY_TICKER_TYPE_VALUE'] == 16 && $shablon['PROPERTY_CITY_VALUE'] != null && $USER->IsAuthorized()) {
                    ?>
                    <a data-fancybox="" data-src="#double-16" href="javascript:" class="city__add">Дублировать
                        баннеры</a>

                    <!-- Дублирование -->
                    <div style="display: none;" id="double-16" class="double">
                        <p class="double__title">Доступно для дублирования</p>
                        <p class="double__comment">При дублировании баннеров из других городов может измениться
                            стоимость баннера, исходя из тарифов города</p>
                        <? foreach ($arShablon as $key => $block) {
                            if ($arShablon[$key]['PROPERTY_CITY_VALUE'] == $arShablon[$key + 1]['PROPERTY_CITY_VALUE']) {
                                continue;
                            } else {?>
                                <div class="double__block">
                                    <p class="double__city"><?= $res = CIBlockElement::GetByID($arShablon[$key]['PROPERTY_CITY_VALUE']);
                                        if ($ar_res = $res->GetNext())
                                            echo $ar_res['NAME']; ?></p>
                                    <div class="basket__table-wrap">
                                        <table class="basket__table">
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="checkbox" class="checkbox">
                                                    <label for="checkbox" class="checkbox-label"></label>
                                                </th>
                                                <th>Канал</th>
                                                <th></th>
                                                <th></th>
                                                <th>Период</th>
                                                <th>Хронометраж</th>
                                                <th>Выходов в день</th>
                                                <th>Текст</th>
                                            </tr>
                                            <? foreach ($arShablon as $info) {
                                                if ($info['PROPERTY_CITY_VALUE'] == $arShablon[$key]['PROPERTY_CITY_VALUE']) {
                                                    $tickerInfo = json_decode(htmlspecialchars_decode($info['PROPERTY_TICKER_INFO_VALUE'])); ?>
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox"
                                                                   data-chanel="<?= $info['PROPERTY_CHANEL_VALUE'] ?>"
                                                                   data-ticker-info="<?= $info['PROPERTY_TICKER_INFO_VALUE'] ?>"
                                                                   id="double-<?= $info['ID'] ?>"
                                                                   class="checkbox checkbox-double">
                                                            <label for="double-<?= $info['ID'] ?>"
                                                                   class="checkbox-label"></label>
                                                        </td>
                                                        <td><p class="double__name">
                                                                <?= $res = CIBlockElement::GetByID($info['PROPERTY_CHANEL_VALUE']);
                                                                if ($ar_res = $res->GetNext())
                                                                    echo strtok($ar_res['NAME'], ' /'); ?>
                                                                <b>, <?= $info['PROPERTY_CITY_VALUE'] ?></b></p></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>
                                                            <span>Период</span>
                                                            <div class="basket__period"><?= $tickerInfo->DATE_RANGE ?></div>
                                                        </td>
                                                        <td><span>Хронометраж</span><?= $tickerInfo->TICKER_TIME ?><i
                                                                    class="double__sec">секунд</i></td>
                                                        <td><span>Выходов в день</span><?= $tickerInfo->TICKER_NUMBER ?>
                                                        </td>
                                                        <td><span>Текст</span>
                                                            <p class="double__text"><?= $tickerInfo->TICKER_TEXT ?></p>
                                                        </td>
                                                    </tr>
                                                <?
                                                }
                                            } ?>
                                        </table>
                                    </div>
                                </div>
                            <?
                            }
                        } ?>
                        <button type="submit" class="btn btn--blue double__btn disabled" disabled="disabled">Сохранить
                        </button>
                    </div>
                    <? break;
                }
            } ?>
            <div class="city__box">
                <? foreach ($arBanner as $banner): ?>
                    <input type="checkbox" id="<?= $banner['ID'] ?>" name="<?= $banner['ID'] ?>"
                           value="<?= $banner['ID'] ?>" class="checkbox">
                    <label for="<?= $banner['ID'] ?>" class="checkbox-label"><?= $banner['NAME'] ?></label>
                <? endforeach ?>
            </div>
        </div>
    <? endif ?>
    <? if ($arStroka): ?>
        <div class="city__field" id="tabs-2">
            <div class="city__box">
                <? foreach ($arStroka as $stroka): ?>
                    <input type="checkbox" id="<?= $stroka['ID'] ?>" name="<?= $stroka['ID'] ?>"
                           value="<?= $stroka['ID'] ?>" class="checkbox">
                    <label for="<?= $stroka['ID'] ?>" class="checkbox-label"><?= $stroka['NAME'] ?></label>
                <? endforeach ?>
            </div>
        </div>
    <? endif ?>
    <? if ($arRoliki): ?>
        <div class="city__field" id="tabs-3">
            <div class="city__box">
                <? foreach ($arRoliki as $roliki): ?>
                    <input type="checkbox" id="<?= $roliki['ID'] ?>" name="<?= $roliki['ID'] ?>"
                           value="<?= $roliki['ID'] ?>" class="checkbox">
                    <label for="<?= $roliki['ID'] ?>" class="checkbox-label"><?= $roliki['NAME'] ?></label>
                <? endforeach ?>
            </div>
        </div>
    <? endif ?>
    <? if ($arRadio): ?>
        <div class="city__field" id="tabs-4">
            <div class="city__box">
                <? foreach ($arRadio as $radio): ?>
                    <input type="checkbox" id="<?= $radio['ID'] ?>" name="<?= $radio['ID'] ?>"
                           value="<?= $radio['ID'] ?>" class="checkbox">
                    <label for="<?= $radio['ID'] ?>" class="checkbox-label"><?= $radio['NAME'] ?></label>
                <? endforeach ?>
            </div>
        </div>
    <? endif ?>
    <? if ($arInternet): ?>
        <div class="city__field" id="tabs-5">
            <div class="city__box">
                <? foreach ($arInternet as $internet): ?>
                    <input type="checkbox" id="<?= $internet['ID'] ?>" name="<?= $internet['ID'] ?>"
                           value="<?= $internet['ID'] ?>" class="checkbox">
                    <label for="<?= $internet['ID'] ?>" class="checkbox-label"><?= $internet['NAME'] ?></label>
                <? endforeach ?>
            </div>
        </div>
    <? endif ?>
    <? if ($arNewsPaper): ?>
        <div class="city__field" id="tabs-6">
            <div class="city__box">
                <? foreach ($arNewsPaper as $newsPaper): ?>
                    <input type="checkbox" id="<?= $newsPaper['ID'] ?>" name="<?= $newsPaper['ID'] ?>"
                           value="<?= $newsPaper['ID'] ?>" class="checkbox">
                    <label for="<?= $newsPaper['ID'] ?>" class="checkbox-label"><?= $newsPaper['NAME'] ?></label>
                <? endforeach ?>
            </div>
        </div>
    <? endif ?>
</div>


  