<?php

/**
 * @author Jay Trees <github.jay@grandel.anonaddy.me>
 */

namespace wishthis;

$scripts = array(
    '/src/assets/js/parts/wishlist-filter.js',
    '/src/assets/js/parts/wishlists.js',
);
?>
<?php foreach ($scripts as $script) { ?>
    <script defer src="<?= $script ?>?m=<?= filemtime(ROOT . $script) ?>"></script>
<?php } ?>

<div>
    <div class="ui stackable grid">
        <div class="column">

            <div class="ui floating dropdown labeled icon button filter priority">
                <input type="hidden" name="filters" />

                <i class="filter icon"></i>
                <span class="text"><?= __('Filter priorities') ?></span>

                <div class="menu">
                    <div class="ui icon search input">
                        <i class="search icon"></i>
                        <input type="text" placeholder="<?= __('Search priorities') ?>" />
                    </div>

                    <div class="divider"></div>

                    <div class="header">
                        <i class="filter icon"></i>
                        <?= __('Priorities') ?>
                    </div>

                    <div class="scrolling menu">
                        <div class="item" data-value="-1">
                            <i class="ui empty circular label"></i>
                            <?= __('All priorities') ?>
                        </div>

                        <div class="item" data-value="">
                            <i class="ui white empty circular label"></i>
                            <?= __('No priority') ?>
                        </div>

                        <div class="divider"></div>

                        <?php foreach (Wish::$priorities as $number => $priority) { ?>
                            <div class="item" data-value="<?= $number ?>">
                                <i class="ui <?= $priority['color'] ?> empty circular label"></i>
                                <?= $priority['name'] ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <input type="hidden" name="style" value="grid" />
            <div class="ui icon buttons view">
                <button class="ui button" value="grid"><i class="grip horizontal icon"></i></button>
                <button class="ui button" value="list"><i class="grip lines icon"></i></button>
            </div>

        </div>
    </div>
</div>

<?php if (isset($wishlist->id)) { ?>
    <div class="wishlist-cards" data-wishlist="<?= $wishlist->id ?>"></div>
<?php } else { ?>
    <div class="wishlist-cards"></div>
<?php } ?>