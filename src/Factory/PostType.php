<?php

namespace MadeByLess\Lessi\Factory;

use MadeByLess\Lessi\Helper\HelperTrait;
use MadeByLess\Lessi\Helper\HookTrait;

/**
 * Post Type Factory
 */
class PostType
{
    use HelperTrait;
    use HookTrait;

    /**
     * Post type singular name
     *
     * @var string
     */
    private string $singularName;

    /**
     * Post type plural name
     *
     * @var string
     */
    private string $pluralName;

    /**
     * Post type custom args
     *
     * @var array
     */
    private array $args;

    /**
     * Class constructor
     *
     * @param string $singularName
     * @param string $pluralName
     * @param array  $args
     */
    public function __construct(string $singularName, string $pluralName, array $args = [])
    {
        $this->singularName = $singularName;
        $this->pluralName   = $pluralName;
        $this->args         = $args;
    }

    /**
     * Returns singlular name
     *
     * @return string
     */
    public function getSingularName(): string
    {
        return $this->singularName;
    }

    /**
     * Returns plural name
     *
     * @return string
     */
    public function getPluralName(): string
    {
        return $this->pluralName;
    }

    /**
     * Sanitizes the name to create a slug
     *
     * @param string $name
     *
     * @return string
     */
    private function getSlug(string $name): string
    {
        return sanitize_title($name);
    }

    /**
     * Registers custom post type
     *
     * @return void
     */
    public function register()
    {
        $slug = $this->getSlug($this->getSingularName());

        if (! post_type_exists($slug)) {
            $defaults = $this->getArguments($this->getSingularName(), $this->getPluralName(), 'getPostTypeArguments');
            $args     = array_replace_recursive($defaults, $this->args);

            register_post_type($slug, $args);
        }
    }

    /**
     * Registers custom taxonomy for current post type
     *
     * @param string $singularName
     * @param string $pluralName
     * @param array  $args
     *
     * @return void
     */
    public function addTaxonomy(string $singularName, string $pluralName, array $args = [])
    {
        $slug         = $this->getSlug($singularName);
        $postTypeSlug = $this->getSlug($this->getSingularName());

        if (! taxonomy_exists($slug)) {
            // Create taxonomy and attach it to the object type (post type).
            $defaults = $this->getArguments($singularName, $pluralName, 'getTaxonomyArguments');
            $args     = array_replace_recursive($defaults, $args);

            register_taxonomy($postTypeSlug . '-' . $slug, $postTypeSlug, $args);
        } else {
            // The taxonomy already exists. We are going to attach the existing
            // taxonomy to the object type (post type).
            register_taxonomy_for_object_type($postTypeSlug . '-' . $slug, $postTypeSlug);
        }
    }

    /**
     * Returns a list of arguments to pass to the register function
     *
     * @param string $singularName
     * @param string $pluralName
     * @param string $callback
     *
     * @return array|null
     */
    private function getArguments(string $singularName, string $pluralName, string $callback): ?array
    {
        if (! is_callable([ $this, $callback ])) {
            // TODO: Implement a proper error logging here
            return null;
        }

        $nouns = [
            $singularName,
            strtolower($singularName),
            $pluralName,
            strtolower($pluralName),
        ];

        $slug       = $this->getSlug($singularName);
        $labels     = $this->getGeneratedLabels($nouns);
        $slugOption = get_option($this->buildThemeSlug([ $slug, 'cpt_base' ]));

        return call_user_func([ $this, $callback ], $labels, $slug, $slugOption ?: $slug);
    }

    /**
     * Returns the post type arguments based on the params
     *
     * @param array  $labels
     * @param string $slug
     * @param string $rewriteSlug
     *
     * @return array
     */
    private function getPostTypeArguments(array $labels, string $slug, string $rewriteSlug): array
    {
        return [
            'labels'             => $labels,
            'rest_base'          => $slug,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'query_var'          => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'supports'           => [
                'title',
                'editor',
                'excerpt',
                'author',
                'thumbnail',
                'comments',
                'publicize',
                'revisions',
            ],
            'rewrite'            => [
                'slug'       => $rewriteSlug,
                'with_front' => false,
            ],
        ];
    }

    /**
     * Returns the taxonomy arguments based on the params
     *
     * @param array  $labels
     * @param string $slug
     * @param string $rewriteSlug
     *
     * @return array
     */
    private function getTaxonomyArguments(array $labels, string $slug, string $rewriteSlug): array
    {
        return [
            'labels'            => $labels,
            'rest_base'         => $slug,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_in_rest'      => true,
            'query_var'         => true,
            'hierarchical'      => true,
            'rewrite'           => [
                'slug'         => $rewriteSlug,
                'hierarchical' => true,
                'with_front'   => false,
            ],
        ];
    }

    /**
     * Generates custom labels based on the provided nouns
     *
     * @param array $nouns
     *
     * @return array
     */
    private function getGeneratedLabels(array $nouns): array
    {
        $labelTemplates = [
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'name'                  => esc_html_x('%3$s', 'Post Type General Name'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'singular_name'         => esc_html_x('%1$s', 'Post Type Singular Name'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'menu_name'             => esc_html__('%3$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'name_admin_bar'        => esc_html__('%1$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'archives'              => esc_html__('%1$s Archives'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'attributes'            => esc_html__('%1$s Attributes'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'parent_item_colon'     => esc_html__('Parent %1$s:'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'all_items'             => esc_html__('All %3$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'add_new_item'          => esc_html__('Add New %1$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'add_new'               => esc_html__('Add New'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'new_item'              => esc_html__('New %1$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'edit_item'             => esc_html__('Edit %1$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'update_item'           => esc_html__('Update %1$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'view_item'             => esc_html__('View %1$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'view_items'            => esc_html__('View %3$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'search_items'          => esc_html__('Search %3$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'not_found'             => esc_html__('Not found'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'not_found_in_trash'    => esc_html__('Not found in Trash'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'featured_image'        => esc_html__('Featured Image'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'set_featured_image'    => esc_html__('Set featured image'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'remove_featured_image' => esc_html__('Remove featured image'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'use_featured_image'    => esc_html__('Use as featured image'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'insert_into_item'      => esc_html__('Insert into %2$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'uploaded_to_this_item' => esc_html__('Uploaded to this %2$s'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'items_list'            => esc_html__('%3$s list'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'items_list_navigation' => esc_html__('%3$s list navigation'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'filter_items_list'     => esc_html__('Filter %4$s list'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'item_link'             => esc_html__('%1$s Link'),
            /* Translators: %1$s uc singular, %2$s lc singular, %3$s uc plural, %4$s lc plural. */
            'item_link_description' => esc_html__('A link to a %2$s'),
            'filter_by_date'        => esc_html__('Filter by date'),
        ];

        return array_map(fn($label) => sprintf($label, ...$nouns), $labelTemplates);
    }
}
