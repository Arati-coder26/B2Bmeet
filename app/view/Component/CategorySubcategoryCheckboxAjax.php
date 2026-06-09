<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CategorySubcategoryCheckboxAjax extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
       public $textlabel,$categoriesInfo,$subcategoryFieldId,$categoryFieldId,$labelsList,$populatedData;
    public function __construct($textlabel,$categoriesList,$subcategoryFieldId,$categoryFieldId,$labelsList,$populatedData)
    {
        //
        $this->textlabel = $textlabel;
        $this->categoriesInfo = $categoriesList;
        $this->subcategoryFieldId=$subcategoryFieldId;
        $this->categoryFieldId = $categoryFieldId;
        $this->labelsList = $labelsList;
        $this->populatedData = $populatedData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.category-subcategory-checkbox-ajax');
    }
}
