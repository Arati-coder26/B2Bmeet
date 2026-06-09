<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CategorySubcategoryAjax extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
        public $textlabel,$categoriesInfo,$subcategoryFieldId,$categoryFieldId,$populatedData;
    public function __construct($textlabel,$categoriesList,$subcategoryFieldId,$categoryFieldId,$populatedData)
    {
        //
        $this->textlabel = $textlabel;
        $this->categoriesInfo = $categoriesList;
        $this->subcategoryFieldId=$subcategoryFieldId;
        $this->categoryFieldId = $categoryFieldId;
        $this->populatedData = $populatedData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.category-subcategory-ajax');
    }
}
