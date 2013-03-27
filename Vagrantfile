# -*- mode: ruby -*-
# vi: set ft=ruby :

# -*- mode: ruby -*-
# vi: set ft=ruby :

def Kernel.is_windows
  processor, platform, *rest = RUBY_PLATFORM.split("-")
  platform == "mingw32"
end

Vagrant::Config.run do |config|
  config.vm.define :main do |main_config|
    main_config.vm.box = "liip-squeeze64"
    main_config.vm.box_url = "http://vagrantbox-public.liip.ch/liip-squeeze64.box"
    main_config.vm.network :hostonly, "192.168.22.22"
    main_config.vm.share_folder "v-root", "/vagrant", ".", :nfs => !Kernel.is_windows
    main_config.vm.provision :puppet, :module_path => "vagrant/puppet/modules" do |puppet|
      puppet.manifests_path = "vagrant/puppet/manifests"
      puppet.manifest_file = "main.pp"
    end
  end
end

