<?php

/**
 * A wishlist.
 *
 * @author Jay Trees <github.jay@grandel.anonaddy.me>
 */

namespace wishthis;

class Wishlist
{
    public array $wishes = array();

    public bool $exists = false;

    public function __construct(int|string $id_or_hash)
    {
        global $database;

        $column;

        if (is_numeric($id_or_hash)) {
            $column = 'id';
        } elseif (is_string($id_or_hash)) {
            $column     = 'hash';
            $id_or_hash = '"' . $id_or_hash . '"';
        }

        /**
         * Get Wishlist
         */
        $columns = $database
        ->query(
            'SELECT *
               FROM `wishlists`
              WHERE `' . $column . '` = ' . $id_or_hash . ';'
        )
        ->fetch();

        if ($columns) {
            $this->exists = true;

            foreach ($columns as $key => $value) {
                $this->$key = $value;
            }
        } else {
            return;
        }

        /**
         * Get Wishes
         */
        $this->wishes = $this->getWishes();
    }

    private function getWishes($sql = array()): array
    {
        global $database;

        $SELECT    = isset($sql['SELECT'])    ? $sql['SELECT']    : Wish::SELECT;
        $FROM      = isset($sql['FROM'])      ? $sql['FROM']      : Wish::FROM;
        $LEFT_JOIN = isset($sql['LEFT_JOIN']) ? $sql['LEFT_JOIN'] : Wish::LEFT_JOIN;
        $WHERE     = isset($sql['WHERE'])     ? $sql['WHERE']     : '`wishlist` = ' . $this->id;
        $ORDER_BY  = isset($sql['ORDER_BY'])  ? $sql['ORDER_BY']  : '`priority` DESC, `url` ASC, `title` ASC';

        $WHERE .= ' AND (`status` != "' . Wish::STATUS_FULFILLED . '" OR `status` IS NULL)';

        $this->wishes = $database
        ->query(
            'SELECT ' . $SELECT . '
               FROM ' . $FROM . '
          LEFT JOIN ' . $LEFT_JOIN . '
              WHERE ' . $WHERE . '
           ORDER BY ' . $ORDER_BY . ';'
        )
        ->fetchAll();

        foreach ($this->wishes as &$wish) {
            $wish = new Wish($wish, false);
        }

        return $this->wishes;
    }

    public function getCards(array $options = array()): string
    {
        ob_start();

        /**
         * Options
         */
        if (!empty($options)) {
            $this->wishes = $this->getWishes($options);
        }

        $style = isset($options['style']) ? $options['style'] : 'grid';

        /**
         * Cards
         */
        switch ($style) {
            case 'list':
                ?>
                <div class="ui one column doubling stackable grid wishlist">
                    <?php if (!empty($this->wishes)) { ?>
                        <?php foreach ($this->wishes as $wish) { ?>
                            <div class="column">
                                <?php
                                $wish->style = $style;

                                echo $wish->getCard($this->user);
                                ?>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="sixteen wide column">
                            <?= Page::info(__('This wishlist seems to be empty.'), __('Empty')); ?>
                        </div>
                    <?php } ?>
                </div>
                <?php
                break;

            default:
                ?>
                <div class="ui three column doubling stackable grid wishlist">
                    <?php if (!empty($this->wishes)) { ?>
                        <?php foreach ($this->wishes as $wish) { ?>
                            <div class="column">
                                <?php
                                $wish->style = $style;

                                echo $wish->getCard($this->user);
                                ?>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="sixteen wide column">
                            <?= Page::info(__('This wishlist seems to be empty.'), __('Empty')); ?>
                        </div>
                    <?php } ?>
                </div>
                <?php
                break;
        }

        $html = ob_get_clean();

        return $html;
    }

    public function getTitle(): string
    {
        $title = __('Wishlist not found');

        if ($this->exists) {
            $title = $this->name;
        }

        return $title;
    }
}
