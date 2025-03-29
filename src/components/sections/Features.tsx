
import React from 'react';
import { LayoutGrid, Zap, Shield, LineChart, Rocket, Sparkles } from 'lucide-react';

interface FeatureCardProps {
  icon: React.ReactNode;
  title: string;
  description: string;
  delay: string;
}

const FeatureCard: React.FC<FeatureCardProps> = ({ icon, title, description, delay }) => {
  return (
    <div 
      className="bg-white rounded-xl p-6 shadow-sm border border-border/50 hover:shadow-md transition-all duration-300 animate-fade-in"
      style={{ animationDelay: delay }}
    >
      <div className="h-12 w-12 bg-primary/10 rounded-lg flex items-center justify-center text-primary mb-4">
        {icon}
      </div>
      <h3 className="text-xl font-semibold mb-2">{title}</h3>
      <p className="text-muted-foreground">{description}</p>
    </div>
  );
};

const Features: React.FC = () => {
  const features = [
    {
      icon: <LayoutGrid className="h-6 w-6" />,
      title: "Responsive Design",
      description: "Create beautiful, responsive layouts that look perfect on any device or screen size.",
      delay: "0s"
    },
    {
      icon: <Zap className="h-6 w-6" />,
      title: "Lightning Fast",
      description: "Optimized for speed with modern performance techniques and efficient code.",
      delay: "0.1s"
    },
    {
      icon: <Shield className="h-6 w-6" />,
      title: "Secure Architecture",
      description: "Built with security in mind, following best practices to protect your data.",
      delay: "0.2s"
    },
    {
      icon: <LineChart className="h-6 w-6" />,
      title: "Analytics",
      description: "Get insights into user behavior with integrated analytics and tracking tools.",
      delay: "0.3s"
    },
    {
      icon: <Rocket className="h-6 w-6" />,
      title: "Easy Deployment",
      description: "Deploy your website with confidence using our streamlined deployment process.",
      delay: "0.4s"
    },
    {
      icon: <Sparkles className="h-6 w-6" />,
      title: "Custom Animations",
      description: "Engage users with smooth, custom animations and interactive elements.",
      delay: "0.5s"
    }
  ];

  return (
    <section id="features" className="py-20 lg:py-32">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold mb-4 animate-fade-in">
            Powerful Features for <span className="text-gradient">Modern Web</span>
          </h2>
          <p className="text-xl text-muted-foreground max-w-2xl mx-auto animate-fade-in" style={{ animationDelay: "0.1s" }}>
            Everything you need to build stunning, high-performance websites that engage users and drive results.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map((feature, index) => (
            <FeatureCard
              key={index}
              icon={feature.icon}
              title={feature.title}
              description={feature.description}
              delay={feature.delay}
            />
          ))}
        </div>
      </div>
    </section>
  );
};

export default Features;
