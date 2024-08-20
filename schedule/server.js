// server.js
const express = require('express');
const stripe = require('stripe')('sk_test_51PpB1gIOn9mt42X65kd42cxzNXAmOKseKJlFOOI1TsrkeSWmCQbcD1MtbQKxrfvA7HrXebgB1q2CVwv7g2w86W8f00v264l1AA'); // Remplacez par votre clé secrète Stripe
const bodyParser = require('body-parser');

const app = express();
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Endpoint pour créer une session de paiement
app.post('/create-checkout-session', async (req, res) => {
    const { serviceId } = req.body;

    // Créez une session de paiement Stripe
    const session = await stripe.checkout.sessions.create({
        payment_method_types: ['card'],
        line_items: [
            {
                price: serviceId, // ID du prix que vous avez configuré dans Stripe
                quantity: 1,
            },
        ],
        mode: 'payment',
        success_url: 'https://votre-domaine.com/success', // Remplacez par votre URL de succès
        cancel_url: 'https://votre-domaine.com/cancel',   // Remplacez par votre URL d'annulation
    });

    res.json({ id: session.id });
});

app.listen(3000, () => console.log('Serveur démarré sur le port 3000'));
