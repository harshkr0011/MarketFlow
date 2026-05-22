<?php

namespace App\Livewire;

use Livewire\Component;

class AiCopywriter extends Component
{
    public $prompt = '';
    public $tone = 'professional';
    public $generatedCopy = '';
    public $isLoading = false;
    public $targetLang = 'en_in';

    public function generate()
    {
        $this->validate([
            'prompt' => 'required|min:3'
        ]);

        $this->isLoading = true;
        
        $gemini = new \App\Services\GeminiService();
        $copy = $gemini->generateCopy($this->prompt, $this->tone);

        // Fallback mock if API key not set or returned error
        if (str_starts_with($copy, 'Error:') || str_starts_with($copy, 'An exception')) {
            $copy = $this->getMockCopy();
        }

        $this->generatedCopy = $copy;
        $this->isLoading = false;
    }

    public function translateCopy()
    {
        if (empty($this->generatedCopy)) {
            return;
        }

        $this->isLoading = true;

        // Simple translation simulator matching key marketing terms
        $translations = [
            'en_in' => "🌱 **Zero-Waste Bamboo Toothbrush: India's Eco-Friendly Choice!**\n\nDid you know that lakhs of plastic toothbrushes clutter our Indian landfills each year? Start your morning routine right with our 100% biodegradable organic bamboo toothbrush.\n\n✨ **Key Benefits:**\n- Charcoal-infused bristles for natural teeth whitening\n- Biodegradable handle crafted from sustainable organic bamboo\n- Made in India, zero plastic, and eco-friendly packaging\n\n👉 **Order today on COD or UPI and get an extra 10% off!**",
            'hinglish' => "🌱 **Zero-Waste Bamboo Toothbrush: Nature ki taraf ek chhota kadam!**\n\nKya aap jante hain ki har saal billions of plastic toothbrushes humare landfills ko ganda karte hain? Apne din ki shuruat kariye humare 100% biodegradable organic bamboo toothbrush ke sath.\n\n✨ **Special features:**\n- Natural whitening ke liye charcoal bristles\n- Mazboot aur eco-friendly organic bamboo body\n- Made in India, 100% plastic-free packaging\n\n👉 **Abhi order karein aur 10% discount paayein! UPI & COD options available.**",
            'hi' => "🌱 **जीरो-वेस्ट बैंबू टूथब्रश: प्रकृति की ओर एक कदम!** \n\nक्या आप जानते हैं कि हर साल अरबों प्लास्टिक टूथब्रश कचरे में चले जाते हैं? हमारे 100% बायोडिग्रेडेबल बैंबू टूथब्रश के साथ अपने दिन की शुरुआत करें।\n\n✨ **विशेषताएं:**\n- पूर्ण जैविक चारकोल ब्रिसल्स\n- टिकाऊ जैविक बांस से निर्मित\n- पर्यावरण अनुकूल पैकेजिंग\n\n👉 **आज ही खरीदें और 10% की छूट पाएं!**",
            'es' => "🌱 **Cepillo de Dientes de Bambú Zero-Waste: ¡Un paso hacia la naturaleza!**\n\n¿Sabías que miles de millones de cepillos de dientes de plástico terminan en los vertederos cada año? Comienza tu día con nuestro cepillo de dientes 100% biodegradable.\n\n✨ **Características:**\n- Cerdas de carbón activado 100% orgánicas\n- Mango ergonómico de bambú sostenible\n- Empaque ecológico compostable\n\n👉 **¡Compra hoy y obtén un 10% de descuento!**",
            'en' => "🌱 **Zero-Waste Bamboo Toothbrush: A Step Towards Nature!**\n\nDid you know that billions of plastic toothbrushes end up in landfills every year? Start your morning routine with our 100% biodegradable organic bamboo toothbrush.\n\n✨ **Key Benefits:**\n- Infused charcoal bristles for natural whitening\n- FSC-certified ergonomic bamboo handle\n- Zero plastic, compostable packaging\n\n👉 **Shop now and save 10% on your first bundle!**"
        ];

        if (array_key_exists($this->targetLang, $translations)) {
            $this->generatedCopy = $translations[$this->targetLang];
        }

        $this->isLoading = false;
    }

    private function getMockCopy()
    {
        return "🌱 **Zero-Waste Bamboo Toothbrush: India's Eco-Friendly Choice!**\n\nDid you know that lakhs of plastic toothbrushes clutter our Indian landfills each year? Start your morning routine right with our 100% biodegradable organic bamboo toothbrush.\n\n✨ **Key Benefits:**\n- Charcoal-infused bristles for natural teeth whitening\n- Biodegradable handle crafted from sustainable organic bamboo\n- Made in India, zero plastic, and eco-friendly packaging\n\n👉 **Order today on COD or UPI and get an extra 10% off!**";
    }

    public function render()
    {
        return view('livewire.ai-copywriter');
    }
}
