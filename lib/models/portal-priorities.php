<?php
function portal_get_priorities_list() {

    return apply_filters( 'portal_priorities_list', array(
        'normal' => array(
                'label' =>  __( 'Normal', 'portal_projects' ),
                'slug'  =>  'normal',
                'color' =>  '#99C262',
                'value' =>  2,
        ),
        'low'   => array(
            'label' =>  __( 'Low', 'portal_projects' ),
            'slug'  =>  'low',
            'color' =>  '#CDD7B6',
            'value' => 4,
        ),
        'medium' => array(
            'label' =>  __( 'Medium', 'portal_projects' ),
            'slug'  =>  'medium',
            'color' =>  '#FBB829',
            'value' =>  3,
        ),
        'high' => array(
            'label' =>  __( 'High', 'portal_projects' ),
            'slug'  =>  'high',
            'color' =>  '#c44d58',
            'value' =>  1
        ),
    ));

}
