
import React from 'react';
import { Button } from '@/components/ui/button';
import { ArrowRight } from 'lucide-react';

const Hero: React.FC = () => {
  return (
    <section className="relative pt-20 lg:pt-28 pb-20 hero-gradient">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col items-center text-center">
          <div className="inline-flex items-center px-4 py-2 rounded-full bg-brand-purple/10 text-brand-purple mb-6 animate-fade-in">
            <span className="text-sm font-medium">Introducing Nexus - The Future of Web</span>
          </div>
          
          <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 max-w-4xl animate-fade-in" style={{ animationDelay: "0.1s" }}>
            Create <span className="text-gradient">Stunning Websites</span> with Modern Technology
          </h1>
          
          <p className="text-xl text-muted-foreground mb-8 max-w-2xl animate-fade-in" style={{ animationDelay: "0.2s" }}>
            Build scalable, high-performance web applications with cutting-edge tools and frameworks. Elevate your digital presence with our innovative solutions.
          </p>
          
          <div className="flex flex-col sm:flex-row gap-4 animate-fade-in" style={{ animationDelay: "0.3s" }}>
            <Button size="lg" className="px-8 py-6">
              Get Started
              <ArrowRight className="ml-2 h-5 w-5" />
            </Button>
            <Button size="lg" variant="outline" className="px-8 py-6">
              View Demo
            </Button>
          </div>
        </div>
        
        <div className="mt-16 lg:mt-24 relative animate-fade-in" style={{ animationDelay: "0.4s" }}>
          <div className="animated-border">
            <div className="w-full h-[300px] md:h-[400px] lg:h-[500px] rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
              <div className="text-gradient text-xl font-semibold">
                [Dashboard Preview]
              </div>
            </div>
          </div>
          
          {/* Floating Elements for Visual Enhancement */}
          <div className="absolute -top-8 -left-8 w-16 h-16 bg-brand-purple/20 rounded-full animate-float" style={{ animationDelay: "0s" }}></div>
          <div className="absolute top-1/4 -right-8 w-20 h-20 bg-brand-blue/20 rounded-full animate-float" style={{ animationDelay: "1s" }}></div>
          <div className="absolute bottom-1/4 -left-12 w-24 h-24 bg-brand-indigo/20 rounded-full animate-float" style={{ animationDelay: "2s" }}></div>
          <div className="absolute -bottom-10 right-1/4 w-16 h-16 bg-brand-lightBlue/20 rounded-full animate-float" style={{ animationDelay: "1.5s" }}></div>
        </div>
      </div>
    </section>
  );
};

export default Hero;
